<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

// Create a category selector
class JFormFieldCategories extends JFormField {
	
	var	$type = 'categories';
	
	function getInput(){
		$categories=array();
		$categories = JHtml::_('category.options', 'com_content');
		
		// Create the 'all categories' listing
		$option = new JObject;
		$option->value = '';
		$option->text = JText::_('DUOSHUO_SELECT_ALL_CATEGORIES');
		array_unshift($categories,  $option);

		// Output
		return JHTML::_('select.genericlist', $categories,
			$this->name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="12"',
			'value', 'text', $this->value );
	}
	
}