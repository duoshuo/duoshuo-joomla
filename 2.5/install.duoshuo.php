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

// --- Joomla! 1.6+ ---
// Modules
$modules = &$this->manifest->xpath('modules/module');
foreach($modules as $module){
	$mname = $module->getAttribute('module');
	$client = $module->getAttribute('client');
	if(is_null($client)) $client = 'site';
	($client=='administrator') ? $path=$src.DS.'administrator'.DS.'modules'.DS.$mname : $path = $src.DS.'modules'.DS.$mname;
	$installer = new JInstaller;
	$result = $installer->install($path);
	$status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
}

// Plugins
$plugins = &$this->manifest->xpath('plugins/plugin');
foreach($plugins as $plugin){
	$pname = $plugin->getAttribute('plugin');
	$pgroup = $plugin->getAttribute('group');
	$path = $src.DS.'plugins'.DS.$pgroup;
	$installer = new JInstaller;
	$result = $installer->install($path);
	$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
	$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
	$db->setQuery($query);
	$db->query();
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
