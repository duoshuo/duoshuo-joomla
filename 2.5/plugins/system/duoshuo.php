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

jimport( 'joomla.plugin.plugin' );
jimport('joomla.html.parameter');


class plgSystemDuoshuo extends JPlugin {

  // duoshuo reference parameters
	var $plg_name				= "duoshuo";
	var $plg_copyrights_start	= "\n\n<!-- Duoshuo Comment BEGIN --> \n";
	var $plg_copyrights_end		= "\n\n<!-- Duoshuo Comment END --> \n";
	
	function plgSystemDuoshuo( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	function onAfterRender() {

		// API
		$mainframe	= &JFactory::getApplication();
		$document 	= &JFactory::getDocument();

		// Assign paths
		$sitePath = JPATH_SITE;
		$siteUrl  = JURI::root(true);

		// Requests
		$option 		= JRequest::getCmd('option');
		$view 			= JRequest::getCmd('view');
		$layout 		= JRequest::getCmd('layout');
		$page 			= JRequest::getCmd('page');
		$secid 			= JRequest::getInt('secid');
		$catid 			= JRequest::getInt('catid');
		$itemid 		= JRequest::getInt('Itemid');
		if(!$itemid) $itemid = 999999;

		// Check if plugin is enabled
		if(JPluginHelper::isEnabled('system',$this->plg_name)==false) return;

		// Quick check to decide whether to render the plugin or not
		if(strpos(JResponse::getBody(),'Duoshuo')===false) return;
		
		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_system_'.$this->plg_name, JPATH_ADMINISTRATOR);

		// Admin check
		if($mainframe->isAdmin()) return;



		// ----------------------------------- Get plugin parameters -----------------------------------
		$plugin =& JPluginHelper::getPlugin('content', $this->plg_name);
		$pluginParams = new JParameter( $plugin->params );

		$duoshuoSubDomain	= trim($pluginParams->get('duoshuoSubDomain',''));

		if(!$duoshuoSubDomain){
			// Quick check before we proceed
			return;
		} else {
			// Perform some parameter cleanups
			$duoshuoSubDomain = str_replace(array('http://','.duoshuo.com/','.duoshuo.com'), array('','',''), $duoshuoSubDomain);
		}

		// Append head includes only when the document is in HTML mode
		if(JRequest::getCmd('format')=='html' || JRequest::getCmd('format')==''){
			$elementToGrab = '</body>';
			$htmlToInsert = "
				<!-- Duoshuo Script BEGIN -->
				<script type=\"text/javascript\">
				var duoshuoQuery = {short_name:\"$duoshuoSubDomain\"};
				(function() {
					var ds = document.createElement('script');
					ds.type = 'text/javascript';ds.async = true;
					ds.src = 'http://static.duoshuo.com/embed.js';
					ds.charset = 'UTF-8';
					(document.getElementsByTagName('head')[0] 
					|| document.getElementsByTagName('body')[0]).appendChild(ds);
				})();
				</script>
				<!-- Duoshuo Script END -->
			";

			// Output
			$buffer = JResponse::getBody();
			$buffer = str_replace($elementToGrab, $htmlToInsert."\n\n".$elementToGrab, $buffer);
			JResponse::setBody($buffer);
		}

	} // END FUNCTION

} // END CLASS