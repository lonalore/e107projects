<?php

/**
 * @file
 * Common functions.
 */

if(!defined('e107_INIT'))
{
	require_once('../../../class2.php');
}

/**
 * Performs an HTTP request.
 *
 * This is a flexible and powerful HTTP client implementation. Correctly
 * handles GET, POST, PUT or any other HTTP requests. Handles redirects.
 *
 * @param $url
 *   A string containing a fully qualified URI.
 * @param array $options
 *   (optional) An array that can have one or more of the following elements:
 *   - headers: An array containing request headers to send as name/value pairs.
 *   - method: A string containing the request method. Defaults to 'GET'.
 *   - data: A string containing the request body, formatted as
 *     'param=value&param=value&...'. Defaults to NULL.
 *   - max_redirects: An integer representing how many times a redirect
 *     may be followed. Defaults to 3.
 *   - timeout: A float representing the maximum number of seconds the function
 *     call may take. The default is 30 seconds. If a timeout occurs, the error
 *     code is set to the HTTP_REQUEST_TIMEOUT constant.
 *   - context: A context resource created with stream_context_create().
 *
 * @return object
 *   An object that can have one or more of the following components:
 *   - request: A string containing the request body that was sent.
 *   - code: An integer containing the response status code, or the error code
 *     if an error occurred.
 *   - protocol: The response protocol (e.g. HTTP/1.1 or HTTP/1.0).
 *   - status_message: The status message from the response, if a response was
 *     received.
 *   - redirect_code: If redirected, an integer containing the initial response
 *     status code.
 *   - redirect_url: If redirected, a string containing the URL of the redirect
 *     target.
 *   - error: If an error occurred, the error message. Otherwise not set.
 *   - headers: An array containing the response headers as name/value pairs.
 *     HTTP header names are case-insensitive (RFC 2616, section 4.2), so for
 *     easy access the array keys are returned in lower case.
 *   - data: A string containing the response body that was received.
 */
function e107projects_http_request($url, array $options = array())
{
	$result = new stdClass();

	// Parse the URL and make sure we can handle the schema.
	$uri = @parse_url($url);

	if($uri == false)
	{
		$result->error = 'unable to parse URL';
		$result->code = -1001;
		return $result;
	}

	if(!isset($uri['scheme']))
	{
		$result->error = 'missing schema';
		$result->code = -1002;
		return $result;
	}

	e107projects_timer_start(__FUNCTION__);

	// Merge the default options.
	$options += array(
		'headers'       => array(),
		'method'        => 'GET',
		'data'          => null,
		'max_redirects' => 3,
		'timeout'       => 30.0,
		'context'       => null,
	);

	// Merge the default headers.
	$options['headers'] += array(
		'User-Agent' => 'e107 (+http://e107.org/)',
	);

	// stream_socket_client() requires timeout to be a float.
	$options['timeout'] = (float) $options['timeout'];

	// Use a proxy if one is defined and the host is not on the excluded list.
	$proxy_server = '';
	if($proxy_server && e107projects_http_use_proxy($uri['host']))
	{
		// Set the scheme so we open a socket to the proxy server.
		$uri['scheme'] = 'proxy';
		// Set the path to be the full URL.
		$uri['path'] = $url;
		// Since the URL is passed as the path, we won't use the parsed query.
		unset($uri['query']);

		// Add in username and password to Proxy-Authorization header if needed.
		if($proxy_username = '')
		{
			$proxy_password = '';
			$options['headers']['Proxy-Authorization'] = 'Basic ' . base64_encode($proxy_username . (!empty($proxy_password) ? ":" . $proxy_password : ''));
		}
		// Some proxies reject requests with any User-Agent headers, while others
		// require a specific one.
		$proxy_user_agent = '';
		// The default value matches neither condition.
		if($proxy_user_agent === null)
		{
			unset($options['headers']['User-Agent']);
		}
		elseif($proxy_user_agent)
		{
			$options['headers']['User-Agent'] = $proxy_user_agent;
		}
	}

	switch($uri['scheme'])
	{
		case 'proxy':
			// Make the socket connection to a proxy server.
			$socket = 'tcp://' . $proxy_server . ':' . 8080;
			// The Host header still needs to match the real request.
			$options['headers']['Host'] = $uri['host'];
			$options['headers']['Host'] .= isset($uri['port']) && $uri['port'] != 80 ? ':' . $uri['port'] : '';
			break;

		case 'http':
		case 'feed':
			$port = isset($uri['port']) ? $uri['port'] : 80;
			$socket = 'tcp://' . $uri['host'] . ':' . $port;
			// RFC 2616: "non-standard ports MUST, default ports MAY be included".
			// We don't add the standard port to prevent from breaking rewrite rules
			// checking the host that do not take into account the port number.
			$options['headers']['Host'] = $uri['host'] . ($port != 80 ? ':' . $port : '');
			break;

		case 'https':
			// Note: Only works when PHP is compiled with OpenSSL support.
			$port = isset($uri['port']) ? $uri['port'] : 443;
			$socket = 'ssl://' . $uri['host'] . ':' . $port;
			$options['headers']['Host'] = $uri['host'] . ($port != 443 ? ':' . $port : '');
			break;

		default:
			$result->error = 'invalid schema ' . $uri['scheme'];
			$result->code = -1003;
			return $result;
	}

	if(empty($options['context']))
	{
		$fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout']);
	}
	else
	{
		// Create a stream with context. Allows verification of a SSL certificate.
		$fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout'], STREAM_CLIENT_CONNECT, $options['context']);
	}

	// Make sure the socket opened properly.
	if(!$fp)
	{
		// When a network error occurs, we use a negative number so it does not
		// clash with the HTTP status codes.
		$result->code = -$errno;
		$result->error = trim($errstr) ? trim($errstr) : 'Error opening socket ' . $socket;

		return $result;
	}

	// Construct the path to act on.
	$path = isset($uri['path']) ? $uri['path'] : '/';
	if(isset($uri['query']))
	{
		$path .= '?' . $uri['query'];
	}

	// Only add Content-Length if we actually have any content or if it is a POST
	// or PUT request. Some non-standard servers get confused by Content-Length in
	// at least HEAD/GET requests, and Squid always requires Content-Length in
	// POST/PUT requests.
	$content_length = strlen($options['data']);
	if($content_length > 0 || $options['method'] == 'POST' || $options['method'] == 'PUT')
	{
		$options['headers']['Content-Length'] = $content_length;
	}

	// If the server URL has a user then attempt to use basic authentication.
	if(isset($uri['user']))
	{
		$options['headers']['Authorization'] = 'Basic ' . base64_encode($uri['user'] . (isset($uri['pass']) ? ':' . $uri['pass'] : ':'));
	}

	$request = $options['method'] . ' ' . $path . " HTTP/1.0\r\n";
	foreach($options['headers'] as $name => $value)
	{
		$request .= $name . ': ' . trim($value) . "\r\n";
	}
	$request .= "\r\n" . $options['data'];
	$result->request = $request;
	// Calculate how much time is left of the original timeout value.
	$timeout = $options['timeout'] - e107projects_timer_read(__FUNCTION__) / 1000;
	if($timeout > 0)
	{
		stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
		fwrite($fp, $request);
	}

	// Fetch response. Due to PHP bugs like http://bugs.php.net/bug.php?id=43782
	// and http://bugs.php.net/bug.php?id=46049 we can't rely on feof(), but
	// instead must invoke stream_get_meta_data() each iteration.
	$info = stream_get_meta_data($fp);
	$alive = !$info['eof'] && !$info['timed_out'];
	$response = '';

	while($alive)
	{
		// Calculate how much time is left of the original timeout value.
		$timeout = $options['timeout'] - e107projects_timer_read(__FUNCTION__) / 1000;
		if($timeout <= 0)
		{
			$info['timed_out'] = true;
			break;
		}
		stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
		$chunk = fread($fp, 1024);
		$response .= $chunk;
		$info = stream_get_meta_data($fp);
		$alive = !$info['eof'] && !$info['timed_out'] && $chunk;
	}
	fclose($fp);

	if($info['timed_out'])
	{
		$result->code = -1;
		$result->error = 'request timed out';
		return $result;
	}
	// Parse response headers from the response body.
	// Be tolerant of malformed HTTP responses that separate header and body with
	// \n\n or \r\r instead of \r\n\r\n.
	list($response, $result->data) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
	$response = preg_split("/\r\n|\n|\r/", $response);

	// Parse the response status line.
	$response_status_array = e107projects_parse_response_status(trim(array_shift($response)));
	$result->protocol = $response_status_array['http_version'];
	$result->status_message = $response_status_array['reason_phrase'];
	$code = $response_status_array['response_code'];

	$result->headers = array();

	// Parse the response headers.
	while($line = trim(array_shift($response)))
	{
		list($name, $value) = explode(':', $line, 2);
		$name = strtolower($name);
		if(isset($result->headers[$name]) && $name == 'set-cookie')
		{
			// RFC 2109: the Set-Cookie response header comprises the token Set-
			// Cookie:, followed by a comma-separated list of one or more cookies.
			$result->headers[$name] .= ',' . trim($value);
		}
		else
		{
			$result->headers[$name] = trim($value);
		}
	}

	$responses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
	);
	// RFC 2616 states that all unknown HTTP codes must be treated the same as the
	// base code in their class.
	if(!isset($responses[$code]))
	{
		$code = floor($code / 100) * 100;
	}
	$result->code = $code;

	switch($code)
	{
		case 200: // OK
		case 304: // Not modified
			break;
		case 301: // Moved permanently
		case 302: // Moved temporarily
		case 307: // Moved temporarily
			$location = $result->headers['location'];
			$options['timeout'] -= e107projects_timer_read(__FUNCTION__) / 1000;
			if($options['timeout'] <= 0)
			{
				$result->code = -1;
				$result->error = 'request timed out';
			}
			elseif($options['max_redirects'])
			{
				// Redirect to the new location.
				$options['max_redirects']--;
				$result = e107projects_http_request($location, $options);
				$result->redirect_code = $code;
			}
			if(!isset($result->redirect_url))
			{
				$result->redirect_url = $location;
			}
			break;
		default:
			$result->error = $result->status_message;
	}

	return $result;
}

/**
 * Splits an HTTP response status line into components.
 *
 * See the @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html status
 * line definition @endlink in RFC 2616.
 *
 * @param string $response
 *   The response status line, for example 'HTTP/1.1 500 Internal Server Error'.
 *
 * @return array
 *   Keyed array containing the component parts. If the response is malformed,
 *   all possible parts will be extracted. 'reason_phrase' could be empty.
 *   Possible keys:
 *   - 'http_version'
 *   - 'response_code'
 *   - 'reason_phrase'
 */
function e107projects_parse_response_status($response)
{
	$response_array = explode(' ', trim($response), 3);
	// Set up empty values.
	$result = array(
		'reason_phrase' => '',
	);
	$result['http_version'] = $response_array[0];
	$result['response_code'] = $response_array[1];
	if(isset($response_array[2]))
	{
		$result['reason_phrase'] = $response_array[2];
	}
	return $result;
}

/**
 * Helper function for determining hosts excluded from needing a proxy.
 *
 * @return string $host
 *   TRUE if a proxy should be used for this host.
 */
function e107projects_http_use_proxy($host)
{
	$proxy_exceptions = array('localhost', '127.0.0.1');
	return !in_array(strtolower($host), $proxy_exceptions, true);
}

/**
 * Starts the timer with the specified name.
 *
 * If you start and stop the same timer multiple times, the measured intervals
 * will be accumulated.
 *
 * @param $name
 *   The name of the timer.
 */
function e107projects_timer_start($name)
{
	global $timers;

	$timers[$name]['start'] = microtime(true);
	$timers[$name]['count'] = isset($timers[$name]['count']) ? ++$timers[$name]['count'] : 1;
}

/**
 * Reads the current timer value without stopping the timer.
 *
 * @param $name
 *   The name of the timer.
 *
 * @return string
 *   The current timer value in ms.
 */
function e107projects_timer_read($name)
{
	global $timers;

	if(isset($timers[$name]['start']))
	{
		$stop = microtime(true);
		$diff = round(($stop - $timers[$name]['start']) * 1000, 2);

		if(isset($timers[$name]['time']))
		{
			$diff += $timers[$name]['time'];
		}
		return $diff;
	}

	return $timers[$name]['time'];
}

/**
 * Stops the timer with the specified name.
 *
 * @param $name
 *   The name of the timer.
 *
 * @return
 *   A timer array. The array contains the number of times the timer has been
 *   started and stopped (count) and the accumulated timer value in ms (time).
 */
function e107projects_timer_stop($name)
{
	global $timers;

	if(isset($timers[$name]['start']))
	{
		$stop = microtime(true);
		$diff = round(($stop - $timers[$name]['start']) * 1000, 2);
		if(isset($timers[$name]['time']))
		{
			$timers[$name]['time'] += $diff;
		}
		else
		{
			$timers[$name]['time'] = $diff;
		}
		unset($timers[$name]['start']);
	}

	return $timers[$name];
}

/**
 * Generates unique string for Github secret.
 */
function e107projects_generate_unique_secret_key()
{
	$random = rand();
	$unique = uniqid($random, true);
	return md5($unique);
}

/**
 * Get a user's submitted projects.
 *
 * @param int $user_id
 *  User ID.
 * @param boolean $use_static
 *  true - Use the statically cached results
 *  false - force to refresh cached results
 *
 * @return array
 */
function e107projects_get_user_submitted_projects($user_id, $use_static = true)
{
	static $projects = array();

	if((int) $user_id == 0)
	{
		return $projects;
	}

	if(empty($projects) || $use_static == false)
	{
		$db = e107::getDb();
		$db->select('e107projects_project', '*', 'project_author = ' . (int) $user_id);

		while($row = $db->fetch())
		{
			$projects[$row['project_name']] = $row;
		}
	}

	return $projects;
}

/**
 * Try to get Github Access Token for the current User.
 *
 * @return bool|string
 */
function e107projects_get_access_token()
{
	$hybridAuth = e107::getHybridAuth();
	$adapter = $hybridAuth->getAdapter('Github');
	$conneceted = $hybridAuth->isConnectedWith('Github');

	if(!$conneceted)
	{
		return false;
	}

	$accessToken = $adapter->getAccessToken();

	return varset($accessToken['access_token'], false);
}

/**
 * Try to update Access Token on hooks (are saved in database)
 * belong to the logged in user.
 *
 * @param null $accessToken
 *  If Access Token is given, just update hooks with it.
 */
function e107projects_update_access_token($accessToken = null, $user_id = null)
{
	if(empty($user_id))
	{
		$user_id = defset('USERID', 0);
	}

	if(empty($user_id))
	{
		return;
	}

	if(empty($accessToken))
	{
		$accessToken = e107projects_get_access_token();
	}

	if($accessToken)
	{
		$db = e107::getDb('update_access_token');
		$db->select('e107projects_project', '*', 'project_author = ' . $user_id);

		$projects = array();
		while($row = $db->fetch())
		{
			$user = $row['project_user'];
			$repo = $row['project_name'];

			$projects[] = array(
				'user' => $user,
				'repo' => $repo,
			);
		}

		if(!empty($projects))
		{
			$where = array();

			foreach($projects as $project)
			{
				$where[] = '(hook_project_user = "' . $project['user'] . '" AND hook_project_name = "' . $project['repo'] . '")';
			}

			$db->update('e107projects_hook', array('data' => array(
				'hook_access_token' => $accessToken,
			), 'WHERE'                                    => implode(' OR ', $where)));
		}
	}
}

/**
 * Make a request to Github for checking Access Token.
 *
 * @param $accessToken
 *  Access Token to be checked.
 *
 * @return bool
 */
function e107projects_access_token_is_valid($accessToken)
{
	$url = 'https://api.github.com/rate_limit?access_token=' . $accessToken;
	$response = e107projects_http_request($url);

	if(!empty($response->code) && $response->code == 200)
	{
		return true;
	}

	return false;
}

/**
 * Helper function to retrieve project information from Github, and create
 * a new project.
 *
 * @see submit.php
 *
 * @param int $repository_id
 *  Github Repository ID.
 * @param int|mixed $user_id
 *  User (e107) ID. This user will be the author of the project.
 * @param string $access_token
 *  If Access Token is set, try to create Webhook.
 *
 * @return bool
 */
function e107projects_insert_project($repository_id, $user_id = USERID, $access_token = null)
{
	if(empty($repository_id))
	{
		return false;
	}

	// Load required class.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

	$event = e107::getEvent();
	$tp = e107::getParser();
	$db = e107::getDb();

	// Get plugin preferences.
	$plugPrefs = e107::getPlugConfig('e107projects')->getPref();
	// Get a Github Client.
	$client = new e107projectsGithub($access_token);
	// Get the Github username for current user.
	$username = $client->getGithubUsername($user_id);
	// Get user organizations.
	$organizations = $client->getUserOrganizations($username);
	// Get repositories of the current user.
	$repositories = $client->getUserRepositories($username);

	// Get repositories from organizations.
	foreach($organizations as $organization)
	{
		$orgRepos = $client->getUserRepositories($organization['login']);

		// Remove organization repository where no admin permission.
		foreach($orgRepos as $key => $orgRepo)
		{
			if(!varset($orgRepo['permissions']['admin'], false))
			{
				unset($orgRepos[$key]);
			}
		}

		// Get repositories of the organization.
		$repositories = array_merge($repositories, $orgRepos);
	}

	// Remove forked repositories from the list.
	foreach($repositories as $key => $repo)
	{
		if($repo['fork'] == true || $repo['private'] == true)
		{
			unset($repositories[$key]);
		}
	}

	$repository = false;

	foreach($repositories as $repo)
	{
		if($repository_id == $repo['id'])
		{
			$repository = $repo;
		}
	}

	if(!$repository)
	{
		return false;
	}

	// Get the number of commits.
	$commits = $client->countCommits($repository['owner']['login'], $repository['name']);
	// Get the README URL.
	$readmeURL = $client->getReadmeURL($repository['owner']['login'], $repository['name']);
	// Try to get README file contents.
	$readme = file_get_contents($readmeURL);

	$type = e107projects_get_project_type($repository['owner']['login'], $repository['name'], $repository['default_branch']);

	// Prepare arguments for SQL query.
	$project = array(
		'project_id'             => (int) $repository['id'],
		'project_author'         => $user_id,
		'project_user'           => $tp->toDB($repository['owner']['login']),
		'project_name'           => $tp->toDB($repository['name']),
		'project_description'    => $tp->toDB($repository['description']),
		'project_stars'          => (int) $repository['stargazers_count'],
		'project_watchers'       => (int) $repository['watchers'],
		'project_forks'          => (int) $repository['forks'],
		'project_open_issues'    => (int) $repository['open_issues'],
		'project_commits'        => (int) $commits,
		'project_default_branch' => $tp->toDB($repository['default_branch']),
		'project_status'         => 0,
		'project_submitted'      => time(),
		'project_updated'        => time(),
		'project_readme'         => $readme ? $readme : '',
		'project_type'           => $type,
	);

	// Try to save project into database.
	if(!$db->insert('e107projects_project', array('data' => $project), false))
	{
		return false;
	}

	if($access_token)
	{
		$client->createHook($repository['owner']['login'], $repository['name']);
	}

	// Triggering event.
	$event->trigger('e107projects_user_project_submitted', $project);

	e107projects_manage_contributions($repository['owner']['login'], $repository['name'], $repository_id, $access_token);
	e107projects_manage_releases($repository['owner']['login'], $repository['name'], $repository_id, $access_token);
	e107projects_manage_e107org_releases($repository['owner']['login'], $repository['name'], $repository_id, $type);

	return true;
}

/**
 * Helper function to retrieve project information from Github, and update
 * an existing project.
 *
 * @see cron.php
 *
 * @param int $repository_id
 *  Github Repository ID.
 * @param string $access_token
 *
 * @return bool
 */
function e107projects_update_project($repository_id, $access_token = null)
{
	if(empty($repository_id))
	{
		return false;
	}

	$event = e107::getEvent();
	$tp = e107::getParser();
	$db = e107::getDb();

	$localRepo = $db->retrieve('e107projects_project', '*', 'project_id = ' . (int) $repository_id);

	if(!varset($localRepo['project_author'])) // e107 user ID.
	{
		return false;
	}

	$user_id = (int) $localRepo['project_author'];

	// Try to get Access Token.
	$accessToken = $db->retrieve('e107projects_hook', '*', 'hook_project_user = "' . $localRepo['project_user'] . '" AND hook_project_name = "' . $localRepo['project_name'] . '" ');

	if(varset($accessToken['hook_access_token'], false))
	{
		$access_token = $accessToken['hook_access_token'];
	}

	// Load required class.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

	// Get plugin preferences.
	$plugPrefs = e107::getPlugConfig('e107projects')->getPref();
	// Get a Github Client.
	$client = new e107projectsGithub($access_token);
	// Get the Github username for current user.
	$username = $client->getGithubUsername($user_id);
	// Get user organizations.
	$organizations = $client->getUserOrganizations($username);
	// Get repositories of the current user.
	$repositories = $client->getUserRepositories($username);

	// Get repositories from organizations.
	foreach($organizations as $organization)
	{
		$orgRepos = $client->getUserRepositories($organization['login']);

		// Remove organization repository where no admin permission.
		foreach($orgRepos as $key => $orgRepo)
		{
			if(!varset($orgRepo['permissions']['admin'], false))
			{
				unset($orgRepos[$key]);
			}
		}

		// Get repositories of the organization.
		$repositories = array_merge($repositories, $orgRepos);
	}

	// Remove forked repositories from the list.
	foreach($repositories as $key => $repo)
	{
		if($repo['fork'] == true || $repo['private'] == true)
		{
			unset($repositories[$key]);
		}
	}

	$repository = false;

	foreach($repositories as $repo)
	{
		if($repository_id == $repo['id'])
		{
			$repository = $repo;
		}
	}

	if(!$repository)
	{
		// Prepare arguments for SQL query.
		$project = array(
			'project_updated' => time(),
		);

		// Try to update project details in database.
		$db->update('e107projects_project', array(
			'data'  => $project,
			'WHERE' => 'project_id = ' . (int) $repository_id,
		));

		return false;
	}

	// Get the number of commits.
	$commits = $client->countCommits($repository['owner']['login'], $repository['name']);
	// Get the README URL.
	$readmeURL = $client->getReadmeURL($repository['owner']['login'], $repository['name']);
	// Try to get README file contents.
	$readme = file_get_contents($readmeURL);

	$type = e107projects_get_project_type($repository['owner']['login'], $repository['name'], $repository['default_branch']);

	// Prepare arguments for SQL query.
	$project = array(
		// 'project_id'          => (int) $repository['id'],
		'project_author'         => $user_id,
		'project_user'           => $tp->toDB($repository['owner']['login']),
		'project_name'           => $tp->toDB($repository['name']),
		'project_description'    => $tp->toDB($repository['description']),
		'project_stars'          => (int) $repository['stargazers_count'],
		'project_watchers'       => (int) $repository['watchers'],
		'project_forks'          => (int) $repository['forks'],
		'project_open_issues'    => (int) $repository['open_issues'],
		'project_commits'        => (int) $commits,
		'project_default_branch' => $tp->toDB($repository['default_branch']),
		// 'project_status'      => 0,
		// 'project_submitted'   => time(),
		'project_updated'        => time(),
		'project_readme'         => $readme ? $readme : '',
		'project_type'           => $type,
	);

	// Try to update project details in database.
	if(!$db->update('e107projects_project', array('data' => $project, 'WHERE' => 'project_id = ' . (int) $repository_id)))
	{
		return false;
	}

	// Triggering event.
	$event->trigger('e107projects_user_project_updated', $project);

	e107projects_manage_contributions($repository['owner']['login'], $repository['name'], $repository_id, $access_token);
	e107projects_manage_releases($repository['owner']['login'], $repository['name'], $repository_id, $access_token);
	e107projects_manage_e107org_releases($repository['owner']['login'], $repository['name'], $repository_id, $type);

	return true;
}

/**
 * Manage contributions and contributors.
 *
 * @param string $owner
 *  The user who owns the repository.
 * @param string $repository
 *  The name of the repository.
 * @param int $repository_id
 *  The ID of the repository.
 */
function e107projects_manage_contributions($owner, $repository, $repository_id, $access_token = null)
{
	if(empty($owner) || empty($repository || empty($repository_id)))
	{
		return;
	}

	// Load required class.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

	// Get a Github Client.
	$client = new e107projectsGithub($access_token);
	// Get contributions.
	$contributions = $client->getContributors($owner, $repository);

	if(empty($contributions))
	{
		return;
	}

	$db = e107::getDb();
	$tp = e107::getParser();

	// Get existing contributors.
	$db->select('e107projects_contributor', 'contributor_gid');

	$contributors = array();
	while($row = $db->fetch())
	{
		$contributors[] = $row['contributor_gid'];
	}

	// Delete contributions by the selected repository.
	$db->delete('e107projects_contribution', 'project_user = "' . $tp->toDB($owner) . '" AND project_name = "' . $tp->toDB($repository) . '" ');

	foreach($contributions as $contribution)
	{
		// Insert contribution.
		$insert = array(
			'project_id'       => (int) $repository_id,
			'project_user'     => $tp->toDB($owner),
			'project_name'     => $tp->toDB($repository),
			'contributor_id'   => (int) $contribution['id'],
			'contributor_name' => $tp->toDB($contribution['login']),
			'contributions'    => (int) $contribution['contributions'],
		);

		$db->insert('e107projects_contribution', array('data' => $insert), false);

		if(!in_array($contribution['id'], $contributors))
		{
			// Insert contributor.
			$insert = array(
				'contributor_id'     => 0, // e107 ID not associated yet...
				'contributor_gid'    => (int) $contribution['id'],
				'contributor_name'   => $tp->toDB($contribution['login']),
				'contributor_avatar' => $tp->toDB($contribution['avatar_url']),
				'contributor_type'   => $tp->toDB($contribution['type']),
			);

			$db->insert('e107projects_contributor', array('data' => $insert), false);
		}
	}

}

/**
 * Try to detect the type of project.
 *
 * @param string $owner
 *  The user who owns the repository.
 * @param string $repository
 *  The name of the repository.
 * @param string $branch
 *  Default branch for the repository.
 *
 * @return int
 *  1: Plugin
 *  2: Theme
 *  9: Other
 */
function e107projects_get_project_type($owner, $repository, $branch)
{
	$url = 'https://raw.githubusercontent.com/' . $owner . '/' . $repository . '/' . $branch . '/';

	$response = e107projects_http_request($url . 'plugin.xml');
	if(!empty($response->code) && $response->code == 200)
	{
		return 1;
	}

	$response = e107projects_http_request($url . 'theme.xml');
	if(!empty($response->code) && $response->code == 200)
	{
		return 2;
	}

	return 9;
}

/**
 * Get contributions for repository.
 *
 * @param string $user
 *  Github username.
 * @param string $repository
 *  Repository name.
 *
 * @return array
 *  Array contains contributions. Or empty array.
 */
function e107projects_get_contributions($user, $repository)
{
	$db = e107::getDb();
	$tp = e107::getParser();

	$query = 'SELECT 
		c.contributor_name,
		c.contributions,
		cr.contributor_id,
		cr.contributor_avatar
	FROM #e107projects_contribution AS c
	LEFT JOIN #e107projects_contributor AS cr ON c.contributor_id = cr.contributor_gid
	WHERE c.project_user = "' . $tp->toDB($user) . '" AND c.project_name = "' . $tp->toDB($repository) . '" 
	ORDER BY c.contributions DESC ';

	$db->gen($query, false);

	$contributions = array();

	while($row = $db->fetch())
	{
		$contributions[] = $row;
	}

	return $contributions;
}

/**
 * Manage releases.
 *
 * @param string $owner
 *  The user who owns the repository.
 * @param string $repository
 *  The name of the repository.
 * @param int $repository_id
 *  The ID of the repository.
 */
function e107projects_manage_releases($owner, $repository, $repository_id, $access_token = null)
{
	if(empty($owner) || empty($repository || empty($repository_id)))
	{
		return;
	}

	// Load required class.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

	// Get a Github Client.
	$client = new e107projectsGithub($access_token);
	// Get releases.
	$releases = $client->getReleases($owner, $repository);

	if(empty($releases))
	{
		return;
	}

	$db = e107::getDb();
	$tp = e107::getParser();

	// Delete releases by the selected repository.
	$db->delete('e107projects_release', 'release_project_user = "' . $tp->toDB($owner) . '" AND release_project_name = "' . $tp->toDB($repository) . '" ');

	foreach($releases as $release)
	{
		// Insert release.
		$insert = array(
			'release_id'           => (int) $release['id'],
			'release_project_id'   => (int) $repository_id,
			'release_project_user' => $tp->toDB($owner),
			'release_project_name' => $tp->toDB($repository),
			'release_tag_name'     => $tp->toDB($release['tag_name']),
			'release_name'         => $tp->toDB($release['name']),
			'release_draft'        => (int) $release['draft'],
			'release_author_id'    => (int) $release['author']['id'],
			'release_author_name'  => $tp->toDB($release['author']['login']),
			'release_prerelease'   => (int) $release['prerelease'],
			'release_created_at'   => strtotime($release['created_at']),
			'release_published_at' => strtotime($release['published_at']),
		);

		$db->insert('e107projects_release', array('data' => $insert), false);
	}

}

/**
 * Manage e107.org releases.
 *
 * @param string $owner
 *  The user who owns the repository.
 * @param string $repository
 *  The name of the repository.
 * @param int $repository_id
 *  The ID of the repository.
 * @param int $project_type
 *  Project type. 9 - Other, 1 - Plugin, 2 - Theme
 */
function e107projects_manage_e107org_releases($owner, $repository, $repository_id, $project_type)
{
	if(empty($owner) || empty($repository) || empty($repository_id) || empty($project_type))
	{
		return;
	}

	$type = ($project_type == 1 ? 'plugin' : 'theme');
	$data = e107projects_get_e107org_releases($type);

	$releases = array();

	foreach($data as $folder => $versions)
	{
		if($folder == $repository)
		{
			$releases = $versions;
			break;
		}
	}

	if(empty($releases))
	{
		return;
	}

	$db = e107::getDb();
	$tp = e107::getParser();

	// Delete releases by the selected repository.
	$db->delete('e107projects_e107org_release', 'or_project_user = "' . $tp->toDB($owner) . '" AND or_project_name = "' . $tp->toDB($repository) . '" ');

	foreach($releases as $release)
	{
		// Insert release.
		$insert = array(
			'or_project_id'    => (int) $repository_id,
			'or_project_user'  => $tp->toDB($owner),
			'or_project_name'  => $tp->toDB($repository),
			'or_version'       => $tp->toDB($release['version']),
			'or_compatibility' => (int) $release['compatibility'],
			'or_url'           => $tp->toDB($release['url']),
			'or_date'          => (int) strtotime($release['date']),
		);

		$db->insert('e107projects_e107org_release', array('data' => $insert), false);
	}
}

/**
 * Get releases from e107.org. TODO cache
 *
 * @param string $type
 *  Contributed project type.
 *
 * @return array
 */
function e107projects_get_e107org_releases($type = 'plugin')
{
	$data = array();

	$urlThm = 'http://www.e107.org/feed/?type=theme&limit=1000';
	$urlPlg = 'http://www.e107.org/feed/?type=plugin&limit=1000';

	if($type == 'theme')
	{
		$xmlString = file_get_contents($urlThm);
		if(!empty($xmlString))
		{
			$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
			$xmlJSON = json_encode($xml);
			$xmlArray = json_decode($xmlJSON, true);

			if(isset($xmlArray['theme']))
			{
				foreach($xmlArray['theme'] as $theme)
				{
					if(!isset($data[$theme['@attributes']['folder']]))
					{
						$data[$theme['@attributes']['folder']] = array();
					}

					$data[$theme['@attributes']['folder']][] = $theme['@attributes'];
				}
			}
		}
	}

	if($type == 'plugin')
	{
		$xmlString = file_get_contents($urlPlg);
		if(!empty($xmlString))
		{
			$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
			$xmlJSON = json_encode($xml);
			$xmlArray = json_decode($xmlJSON, true);

			if(isset($xmlArray['plugin']))
			{
				foreach($xmlArray['plugin'] as $plugin)
				{
					if(!isset($data[$plugin['@attributes']['folder']]))
					{
						$data[$plugin['@attributes']['folder']] = array();
					}

					$data[$plugin['@attributes']['folder']][] = $plugin['@attributes'];
				}
			}
		}
	}

	return $data;
}

/**
 * Get user contributions for profile page.
 *
 * @param int $user_id
 *  e107 user ID.
 *
 * @return string
 */
function e107projects_get_user_contributions($user_id)
{
	// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
	e107::lan('e107projects', false, true);

	$db = e107::getDb();
	$db->gen('SELECT c.* FROM #e107projects_contribution AS c
	LEFT JOIN #e107projects_contributor AS cr ON c.contributor_id = cr.contributor_gid
	LEFT JOIN #e107projects_project AS p ON c.project_id = p.project_id
	WHERE cr.contributor_id = ' . (int) $user_id . ' AND p.project_status = 1 
	ORDER BY c.contributions DESC ');

	$html = '';

	$total = 0;
	while($row = $db->fetch())
	{
		$user = $row['project_user'];
		$repo = $row['project_name'];
		$cont = $row['contributions'];

		$total += $cont;

		if($cont > 1)
		{
			$cont .= ' ' . LAN_E107PROJECTS_FRONT_76;
		}
		else
		{
			$cont .= ' ' . LAN_E107PROJECTS_FRONT_77;
		}

		$url = e107::url('e107projects', 'project', array(
			'user'       => $user,
			'repository' => $repo,
		));

		$html .= '<a href="' . $url . '" target="_self">' . $user . '/' . $repo . '</a> (' . $cont . ')';
		$html .= '<br/>';
	}

	if($total > 1)
	{
		$total .= ' ' . LAN_E107PROJECTS_FRONT_76;
	}
	else
	{
		$total .= ' ' . LAN_E107PROJECTS_FRONT_77;
	}

	$html .= LAN_E107PROJECTS_FRONT_30 . ': ' . $total;

	return '<div class="user-profile-contributions">' . $html . '<div class="clear clearfix"></div></div>';
}

/**
 * Get the location details for a user.
 *
 * @param int $user_id
 *  e107 user ID.
 *
 * @return array
 *  Location details.
 */
function e107projects_get_user_location($user_id)
{
	$db = e107::getDb();
	$db->gen("SELECT l.location_name, l.location_lat, l.location_lon FROM #user_extended AS ue 
			LEFT JOIN #e107projects_location AS l ON l.location_name = ue.user_plugin_e107projects_location
			WHERE ue.user_extended_id = " . (int) $user_id);

	$location = array();

	while($row = $db->fetch())
	{
		$location = array(
			'name' => $row['location_name'],
			'lat'  => $row['location_lat'],
			'lon'  => $row['location_lon'],
		);
	}

	return $location;
}
