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

$status = new JObject();
$status->modules = array ();
$status->plugins = array ();


// --- Joomla! 1.6+ ---
// Modules
$modules = & $this->manifest->xpath('modules/module');
foreach($modules as $module){
	$mname = $module->getAttribute('module');
	$client = $module->getAttribute('client');
	$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->Quote($mname)."";
	$db->setQuery($query);
	$IDs = $db->loadResultArray();
	if (count($IDs)) {
		foreach ($IDs as $id) {
			$installer = new JInstaller;
			$result = $installer->uninstall('module', $id);
		}
	}
	$status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
}

// Plugins
$plugins = & $this->manifest->xpath('plugins/plugin');
foreach ($plugins as $plugin) {
	$pname = $plugin->getAttribute('plugin');
	$pgroup = $plugin->getAttribute('group');
	$query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = ".$db->Quote($pname)." AND folder = ".$db->Quote($pgroup);
	$db->setQuery($query);
	$IDs = $db->loadResultArray();
	if (count($IDs)) {
		foreach ($IDs as $id) {
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $id);
		}
	}
	$status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
}
	
$rows = 0;

?>
<h2><?php echo JText::_('COM_DUOSHUO_REMOVAL_STATUS'); ?></h2>
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
			<td><strong><?php echo JText::_('COM_DUOSHUO_REMOVED'); ?></strong></td>
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
			<td><strong><?php echo ($module['result']) ? JText::_('COM_DUOSHUO_REMOVED') : JText::_('COM_DUOSHUO_NOT_REMOVED'); ?></strong></td>
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
			<td><strong><?php echo ($plugin['result']) ? JText::_('COM_DUOSHUO_REMOVED') : JText::_('COM_DUOSHUO_NOT_REMOVED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
