<?php
/**
 * @version		0.1
 * @package		Duoshuo for Joomla! (package)
 * @author		http://www.duoshuo.com
 * @copyright	Copyright (c) 2013 Duoshuo.com All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class Com_duoshuoInstallerScript {
	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__extensions SET enabled = 0 WHERE client_id = 1 AND element = ".$db->Quote($parent->get('element')));
		$db->query();
	}
}
