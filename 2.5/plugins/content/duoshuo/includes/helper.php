<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class DuoshuoHelper {

	// Path overrides for MVC templating
	function getTemplatePath($pluginName,$file){

		$mainframe = &JFactory::getApplication();
		$p = new JObject;
		$pluginGroup = 'content';

		if(file_exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))){
			$p->file = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::root(true).'/templates/'.$mainframe->getTemplate().'/html/'.$pluginName.'/'.$file;
		} else {
			// Joomla! 1.6+
			$p->file = JPATH_SITE.DS.'plugins'.DS.$pluginGroup.DS.$pluginName.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::root(true).'/plugins/'.$pluginGroup.'/'.$pluginName.'/'.$pluginName.'/tmpl/'.$file;
		}
		return $p;
	}

} // end class
