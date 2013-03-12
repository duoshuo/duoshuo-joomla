<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');
$mainframe = &JFactory::getApplication();
$db = & JFactory::getDBO();

// Load language file
$lang = &JFactory::getLanguage();
$lang->load('com_duoshuo');

// Set some variables
$status = new JObject();
$status->modules = array();
$src = $this->parent->getPath('source');
// --- Joomla! 1.5 ---
// Modules
$modules = &$this->manifest->getElementByPath('modules');
if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {
	foreach ($modules->children() as $module) {
		$mname = $module->attributes('module');
		$client = $module->attributes('client');
		if(is_null($client)) $client = 'site';
		($client=='administrator')? $path=$src.DS.'administrator'.DS.'modules'.DS.$mname: $path = $src.DS.'modules'.DS.$mname;
		$installer = new JInstaller;
		$result = $installer->install($path);
		$status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
	}
}

// Plugins
$plugins = &$this->manifest->getElementByPath('plugins');
if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {
	foreach ($plugins->children() as $plugin) {
		$pname = $plugin->attributes('plugin');
		$pgroup = $plugin->attributes('group');
		$path = $src.DS.'plugins'.DS.$pgroup;
		$installer = new JInstaller;
		$result = $installer->install($path);
		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
		$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
		$db->setQuery($query);
		$db->query();
	}
}

// JoomFish! elements
if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements')){
	$elements = &$this->manifest->getElementByPath('joomfish');
	if (is_a($elements, 'JSimpleXMLElement') && count($elements->children())) {
		foreach ($elements->children() as $element) {
			JFile::copy($src.DS.'administrator'.DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS.$element->data(),JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS.$element->data());
		}
	}
}

$rows = 0;

?>
<h2><?php echo JText::_('COM_DUOSHUO_INSTALLATION_STATUS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('COM_DUOSHUO_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('COM_DUOSHUO_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::_('COM_DUOSHUO_COMPONENT'); ?></td>
			<td><strong><?php echo JText::_('COM_DUOSHUO_INSTALLED'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)): ?>
		<tr>
			<th><?php echo JText::_('COM_DUOSHUO_MODULE'); ?></th>
			<th><?php echo JText::_('COM_DUOSHUO_CLIENT'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result']) ? JText::_('COM_DUOSHUO_INSTALLED') : JText::_('COM_DUOSHUO_NOT_INSTALLED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (count($status->plugins)): ?>
		<tr>
			<th><?php echo JText::_('COM_DUOSHUO_PLUGIN'); ?></th>
			<th><?php echo JText::_('COM_DUOSHUO_GROUP'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result']) ? JText::_('COM_DUOSHUO_INSTALLED') : JText::_('COM_DUOSHUO_NOT_INSTALLED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
