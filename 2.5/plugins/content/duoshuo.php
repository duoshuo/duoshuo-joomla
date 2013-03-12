<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );
jimport('joomla.html.parameter');


class plgContentDuoshuo extends JPlugin {

  // duoshuo reference parameters
	var $plg_name				= "duoshuo";
	var $plg_copyrights_start	= "\n\n<!-- Duoshuo Comment BEGIN --> \n";
	var $plg_copyrights_end		= "\n\n<!-- Duoshuo Comment END --> \n";

	function plgContentDuoshuo( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	// Joomla! 1.6+
	function onContentBeforeDisplay($context, &$row, &$params, $limitstart = 0 ){
		// Requests
		$option = JRequest::getCmd('option');
		$view 	= JRequest::getCmd('view');		
		if($view == 'category' || $view == 'featured'){
			$this->onContentPrepare('com_content.article', $row, $params, $limitstart );
		}
		if(isset($row->text)){
			$row->introtext = $row->text;
		}
		return;
	}
	
	// Joomla! 1.6+
	function onContentPrepare($context, &$row, &$params, $page = 0){
		$this->renderDuoshuo($row, $params, $page = 0);
	}

	// The main function
	function renderDuoshuo(&$row, &$params, $page){

		// API
		$mainframe	= &JFactory::getApplication();
		$document 	= &JFactory::getDocument();
		$user				= &JFactory::getUser();

		// Assign paths
		$sitePath 	= JPATH_SITE;
		$siteUrl  	= JURI::root(true);

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
		if(JPluginHelper::isEnabled('content',$this->plg_name)==false) return;

		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);
		
		// Simple checks before parsing the plugin
		$properties = get_object_vars($row);
		if(!array_key_exists('catid',$properties)) return;
		
		if(!$row->id || $option=='com_rokdownloads') return;



		// ----------------------------------- Get plugin parameters -----------------------------------
		$plugin =& JPluginHelper::getPlugin('content', $this->plg_name);
		$pluginParams = new JParameter( $plugin->params );

		$duoshuoSubDomain				= trim($pluginParams->get('duoshuoSubDomain',''));
		$selectedCategories			= $pluginParams->get('selectedCategories','');
		$selectedMenus					= $pluginParams->get('selectedMenus','');
		$duoshuoListingCounter		= $pluginParams->get('duoshuoListingCounter',1);
		$duoshuoArticleCounter		= $pluginParams->get('duoshuoArticleCounter',1);

		// External parameter for controlling plugin layout within modules
		if(!$params) $params = new JParameter(null);
		$parsedInModule = $params->get('parsedInModule');
		
		if(!$duoshuoSubDomain){
			// Quick check before we proceed
			JError::raiseNotice('', JText::_('DUOSHUO_PLEASE_ENTER_YOUR_DUOSHUO_SUBDOMAIN'));
			return;
		} else {
			// Perform some parameter cleanups
			$duoshuoSubDomain = str_replace(array('http://','.duoshuo.com/','.duoshuo.com'), array('','',''), $duoshuoSubDomain);
		}



		// ----------------------------------- Before plugin render -----------------------------------
		
		// Get the current category
		$currectCategory = $row->catid;
		
		// Define plugin category restrictions
		$selectedCategories = (array)$selectedCategories;
		if(sizeof($selectedCategories)==1 && $selectedCategories[0]=='') {
			$categories[] = $currectCategory;
		} else {
			$categories = $selectedCategories;
		}

		// Define plugin menu restrictions
		$selectedMenus = (array)$selectedMenus;
		if(sizeof($selectedMenus)==1 && $selectedMenus[0]=='') {
			$menus[] = $itemid;
		} else {
			$menus = $selectedMenus;
		}



		// ----------------------------------- Prepare elements -----------------------------------

		// Includes
		require_once(dirname(__FILE__).DS.$this->plg_name.DS.'includes'.DS.'helper.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

		// Output object
		$output = new JObject;

		// Article URLs
		$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];

		
		$levels = $user->authorisedLevels();
		if (in_array($row->access, $levels)) {
			if($view == 'article'){
				$itemURL = $row->readmore_link;
			} else {
				$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));
			}
		}
		

		$itemURLbrowser = explode("#",$websiteURL.$_SERVER['REQUEST_URI']);
		$itemURLbrowser = $itemURLbrowser[0];

		// Article URL assignments
		$output->itemURL 			= $websiteURL.$itemURL;
		$output->itemURLrelative 	= $itemURL;
		$output->itemTitle			= $row->title;
		$output->itemURLbrowser		= $itemURLbrowser;
		$output->threadKey =		$row->id;

		// Fetch elements specific to the "article" view only
		if(in_array($currectCategory,$categories) && in_array($itemid,$menus) && $option=='com_content' && $view=='article'){
			$output->comments = "
			<div class=\"ds-thread\" data-thread-key=\"$output->threadKey\" 
			 data-title=\"$output->itemTitle\"
			 data-url=\"$output->itemURL\"></div>
			";
		}

		// ----------------------------------- Render the output -----------------------------------
		if(in_array($currectCategory,$categories) && in_array($itemid,$menus)){
			
			if(!defined('DUOSHUO')) define('DUOSHUO',true);
			
			// Append head includes only when the document is in HTML mode
			if(JRequest::getCmd('format')=='html' || JRequest::getCmd('format')==''){

				// CSS
				$plgCSS = DuoshuoHelper::getTemplatePath($this->plg_name,'css/template.css');
				$plgCSS = $plgCSS->http;

				$document->addStyleSheet($plgCSS);

				// JS
				JHtml::_('behavior.framework');
				
				if(!defined('DUOSHUO_JS')){
					$document->addScriptDeclaration("
						window.addEvent('load',function(){
							// Smooth Scroll
							new SmoothScroll({
								duration: 500
							});
						});
					");
					define('DUOSHUO_JS',true);
				}
			}

			if(($option=='com_content' && $view=='article') && $parsedInModule!=1) {

				// Fetch the template
				ob_start();
				$dsArticlePath = DuoshuoHelper::getTemplatePath($this->plg_name,'article.php');
				$dsArticlePath = $dsArticlePath->file;
				include($dsArticlePath);
				$getArticleTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
				ob_end_clean();

				// Output
				$row->text = $getArticleTemplate;

			} else if($duoshuoListingCounter && (($option=='com_content' && ($view=='frontpage' || $view=="featured" || $view=='section' || $view=='category')) || $parsedInModule==1)) {
				
				// Set '$row->text' to '$row->introtext' for J!1.6+
				$row->text = $row->introtext;
				
				// Fetch the template
				ob_start();
				$dsListingPath = DuoshuoHelper::getTemplatePath($this->plg_name,'listing.php');
				$dsListingPath = $dsListingPath->file;
				include($dsListingPath);
				$getListingTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
				ob_end_clean();

				// Output
				$row->text = $getListingTemplate;

			}

		} // END IF

	} // END FUNCTION

} // END CLASS
