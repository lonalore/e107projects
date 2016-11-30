<?php

/**
 * @file
 * Addon file for extending e_parser.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_parse.
 */
class e107projects_parse
{


	/**
	 * Constructor.
	 */
	function __construct()
	{


	}

	/**
	 * @param string $text
	 *  HTML/text to be processed.
	 * @param string $context
	 *  Current context ie:
	 *  OLDDEFAULT | BODY | TITLE | SUMMARY | DESCRIPTION | WYSIWYG
	 *
	 * @return string
	 */
	function toHtml($text, $context = '')
	{
		if($context == 'BODY')
		{
			// FIXME... does not format properly
			// e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.parsedown.php');
			// $Parsedown = new e107projectsParsedown();
			// $text = $Parsedown->text($text);
		}

		return $text;
	}

}
