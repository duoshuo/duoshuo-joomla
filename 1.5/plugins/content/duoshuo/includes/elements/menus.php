<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

// Create a menu item selector
class JElementMenus extends JElement {

	var	$_name = 'menus';
	
	function fetchElement($name, $value, &$node, $control_name){
		
		$document =& JFactory::getDocument();
		$menus = array();
		
		// Create the 'all menus' listing
		$temp = new JObject;
		$temp->value = '';
		$temp->text = JText::_("DUOSHUO_SELECT_ALL_MENUS");
		
		// Grab all the menus, grouped
		$menus = JHTML::_('menu.linkoptions');

		// Merge the above
		array_unshift($menus,$temp);

		// Output
		$output = JHTML::_('select.genericlist',  $menus, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:90%;" multiple="multiple" size="12"', 'value', 'text', $value );
		
		return $output;	
	}
}