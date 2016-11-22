<?php

/**
 * @file
 * NodeJS API implementations.
 */


/**
 * Class e107projects_nodejs.
 */
class e107projects_nodejs
{

	/**
	 * Define handlers for custom messages received from the NodeJS Server.
	 *
	 * @param string $type
	 *   The type of message received from the NodeJS Server. Serves to identify
	 *   the specific extension of the server that sent the message to the e107
	 *   site. This is set by developers when writing their server extensions.
	 *
	 *   As an example, a plugin implementing this method, and returning a global
	 *   function called "my_plugin_message_handler", will have to implement that
	 *   function as follows:
	 *
	 *   function my_plugin_message_handler($message, &$response) {
	 *     // Do whatever is needed with the message received.
	 *     tell_mom_about_the_message($message);
	 *
	 *     // Tell something back to the NodeJS server.
	 *     $response = array(
	 *       'message' => 'Thanks, I just told my mom about this!';
	 *     );
	 *   }
	 *
	 * @return array
	 *   An array of function names. These functions will be executed sequentially,
	 *   and will receive the original $message from the server, and a $response
	 *   variable passed by reference, which they should use as per they needs. This
	 *   variable is what will be sent back automatically by the nodejs plugin to
	 *   the NodeJS server.
	 */
	public function msgHandlers($type)
	{
		return array();
	}


	/**
	 * Define a list of socket.io channels the user will be automatically added to,
	 * upon being registered / authenticated in the NodeJS server.
	 *
	 * When a user is added to a channel through this function, he will receive then
	 * all messages sent to these channels, without having to call manually the
	 * nodejs_add_user_to_channel() function to get the user added to the channel.
	 *
	 * Note that this method doesn't provide any kind of wildcard capability, so it's
	 * not suitable for all scenarios (e.g: when dealing with channels generated
	 * dynamically, for example based on the url the user is visiting). In those
	 * cases, the user will have to be added through nodejs_add_user_to_channel().
	 *
	 * @param stdClass $account
	 *   The e107 account of the user for which the allowed channels are being
	 *   checked. This may be an anonymous user.
	 *
	 * @return array
	 *   An array of socket.io channels to which the user will be granted access.
	 */
	public function userChannels($account)
	{
		$channels = array();
		return $channels;
	}


	/**
	 * Specifies the list of users that can see presence information (whether a user
	 * is connected to the NodeJS server or not) about a given account.
	 *
	 * @param stdClass $account
	 *   The e107 account of the user whose presence information access is being
	 *   requested. This may be an anonymous user.
	 *
	 * @return array
	 *   An array of User IDs, representing the users that can check the presence
	 *   on the NodeJS server of the user specified in $account.
	 */
	public function userPresenceList($account)
	{
		return array();
	}

}
