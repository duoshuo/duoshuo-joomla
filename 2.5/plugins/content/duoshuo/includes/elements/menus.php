<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import the com_menus helper
require_once realpath(JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php');

class JFormFieldMenus extends JFormField {

	var	$type = 'menus';

	function getInput(){
		
		// Initialize variables.
		$groups = array();
		$menus = array();

		// Initialize some field attributes.
		$menuType = (string) $this->element['menu_type'];
		$published = $this->element['published'] ? explode(',', (string) $this->element['published']) : array();
		$disable = $this->element['disable'] ? explode(',', (string) $this->element['disable']) : array();

		// Get the menu items.
		$items = MenusHelper::getMenuLinks($menuType, 0, 0, $published);

		// Build group for a specific menu type.
		if ($menuType) {
			// Initialize the group.
			$groups[$menuType] = array();

			// Build the options array.
			foreach($items as $link) {
				$groups[$menuType][] = JHtml::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
			}
		}

		// Build groups for all menu types.
		else {
			// Build the groups arrays.
			foreach($items as $menu) {
				// Initialize the group.
				$groups[$menu->menutype] = array();

				// Build the options array.
				foreach($menu->links as $link) {
					$groups[$menu->menutype][] = JHtml::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
				}
			}
		}

		foreach ($groups as $group => $links){
			$menus[]= JHtml::_('select.optgroup', $group);
			foreach($links as $link) {
				$menus[]= $link;
			}
			$menus[]= JHtml::_('select.optgroup', $group);
		}
		
		// Create the 'all menus' listing
		$temp = new JObject;
		$temp->value = '';
		$temp->text = JText::_('DUOSHUO_SELECT_ALL_MENUS');
		
		// Merge the above
		array_unshift($menus,$temp);
		
		// Output
		$output = JHTML::_('select.genericlist',  $menus, $this->name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="12"', 'value', 'text', $this->value );
		return $output;
	}
}
