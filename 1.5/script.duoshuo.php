<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class Com_duoshuoInstallerScript {
	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__extensions SET enabled = 0 WHERE client_id = 1 AND element = ".$db->Quote($parent->get('element')));
		$db->query();
	}
}
