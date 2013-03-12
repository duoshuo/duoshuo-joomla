<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<span id="startOfPage"></span>

<?php if($duoshuoArticleCounter): ?>
<!-- duoshuo comments counter-->
<div class="duoshuoArticleCounter">
	<a href="<?php echo $output->itemURL; ?>#response">
		<span class="ds-thread-count"
		  data-thread-key="<?php echo $output->threadKey; ?>">
		<?php echo JText::_("DUOSHUO_VIEW_COMMENTS"); ?></span>
	</a>
	<div class="clr"></div>
</div>
<?php endif; ?>

<?php echo $row->text; ?>

<!-- duoshuo comments block -->
<?php echo $output->comments; ?>

<div class="clr"></div>
