<?php
/**
* @version    SVN: 
* @package    Com_Tjvendors
* @author     Techjoomla <contact@techjoomla.com>
* @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
* @license    GNU General Public License version 2 or later.
*/
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_tjvendors/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function ()
	{
	});
		

	Joomla.submitbutton = function (task)
	{
		if(task == 'vendorfee.apply' || task == 'vendorfee.save' || task == 'vendorfee.save2new' || task == 'vendorfee.save2copy')
		{
			var username = document.getElementById("jform_vendor_id").value;

			if(username == 'Select a User.')
			{
				var msg = "<?php echo JText::_('COM_TJVENDORS_SELECT_USERNAME'); ?>";
				alert(msg);
				return false;
			}
			else
			{
				Joomla.submitform(task, document.getElementById('vendorfee-form'));
			}
		}
		else if (task == 'vendorfee.cancel')
		{
			Joomla.submitform(task, document.getElementById('vendorfee-form'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('vendorfee-form'));
		}
	}
</script>
<form 
action="
<?php 
echo
JRoute::_('index.php?option=com_tjvendors&layout=edit&id='
. (int) $this->item->vendor_id . '&currency='); ?>"
method="post" enctype="multipart/form-data" name="adminForm" id="vendorfee-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TJVENDORS_TITLE_VENDOR', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->item->vendor_id; ?>" />
						
						<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('vendor_id');?></div>
						<div class="controls"><?php echo $this->item->vendor_title;?></div>
						</div>
						
						<div class = "control-group" >
							<div class = "control-label">
						<label><?php echo JText::_('COM_TJVENDORS_FORM_LBL_VENDOR_CURRENCY'); ?></label>
						</div>
						<div class = "controls">
						
						<?php

						echo JHTML::_('string.truncate', $this->curr);

						?>
						</div>
						</div>
						
						<?php echo $this->form->renderField('percent_commission'); ?>
						<?php echo $this->form->renderField('flat_commission'); ?>
						<?php if ($this->state->params->get('save_history', 1))
						{?><div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('version_note');?></div>
								<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
							</div>
						<?php
						}?>
						
				</fieldset>
			</div>
		</div>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
        
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="vendor_id" value="<?php echo $this->vendor_id; ?>">
		<input type="hidden" name="currency" value="<?php echo $this->curr; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

