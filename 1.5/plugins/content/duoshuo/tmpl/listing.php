<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php echo $row->text; ?>

<a href="<?php echo $output->itemURL; ?>#response">
	<span class="ds-thread-count"
	  data-thread-key="<?php echo $output->threadKey; ?>">
	<?php echo JText::_("DUOSHUO_VIEW_COMMENTS"); ?></span>
</a>