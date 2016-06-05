<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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
		//~ if (task == 'vendor.cancel')
		//~ {
			//~ Joomla.submitform(task, document.getElementById('vendor-form'));
		//~ }
		//~ else
		//~ {
			//~ if (task != 'vendor.cancel' && document.formvalidator.isValid(document.id('vendor-form')))
			//~ {
				//~ Joomla.submitform(task, document.getElementById('vendor-form'));
			//~ }
			//~ else
			//~ {
				//~ alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			//~ }
		//~ }

		if(task == 'vendor.apply' || task == 'vendor.save' || task == 'vendor.save2new' || task == 'vendor.save2copy')
		{
			var username = document.getElementById("jform_user_id").value;

			if(username == 'Select a User.')
			{
				var msg = "<?php echo JText::_('COM_TJVENDORS_SELECT_USERNAME'); ?>";
				alert(msg);
				return false;
			}
			else
			{
				Joomla.submitform(task, document.getElementById('vendor-form'));
			}
		}
		else if (task == 'vendor.cancel')
		{
			Joomla.submitform(task, document.getElementById('vendor-form'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('vendor-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&id=' . (int) $this->item->id  . '&client=' . $this->input->get('client', '', 'STRING')); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="vendor-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TJVENDORS_TITLE_VENDOR', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
						<?php echo $this->form->renderField('user_id'); ?>
						<?php //echo $this->form->renderField('email_id'); ?>
						<?php //echo $this->form->renderField('client'); ?>
						<?php echo $this->form->renderField('percent_commission'); ?>
						<?php echo $this->form->renderField('flat_commission'); ?>
						<?php
						if ($this->state->params->get('save_history', 1))
						{?>
							<div class="control-group">
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

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

