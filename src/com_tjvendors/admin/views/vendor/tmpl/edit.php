<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright  2009-2017 TechJoomla. All rights reserved.
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
<?php
$currUrl = TjvendorsHelpersTjvendors::getCurrency();
?>
<form action="<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' . (int) $this->item->vendor_id  . '&client=' . $this->input->get('client', '', 'STRING') . $currUrl); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="vendor-form" class="form-validate">
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'personal')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'personal', JText::_('COM_TJVENDORS_TITLE_PERSONAL', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->item->vendor_id; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

						<?php echo $this->form->renderField('user_id');

						if (empty($this->input->get('client', '', 'STRING')))
						{
							echo $this->form->renderField('vendor_client');
						}
						else
						{
						?>
						<input type="hidden" name="jform[vendor_client]" value='<?php echo $this->item->vendor_client;?>' />
						<?php
						}
						?>

						<?php echo $this->form->renderField('vendor_title'); ?>
						<?php echo $this->form->renderField('vendor_description'); ?>
						<?php echo $this->form->renderField('vendor_logo'); ?>
						<div class="controls">
						<div class="alert alert-warning">
						<?php
						echo sprintf(JText::_("COM_TJVENDORS_FILE_UPLOAD_ALLOWED_EXTENSIONS"), 'jpg, jpeg, png');
						?>
						</div>
						<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->item->vendor_logo; ?>" />
						<?php if (!empty($this->item->vendor_logo)) : ?>
							<div class="control-group">
								<div class="controls "><img src="<?php echo JUri::root() . $this->item->vendor_logo; ?>"></div>
							</div>
						<?php endif; ?>
					<input type="hidden" name="jform[currency]" value='<?php echo $this->item->currency;?>' />
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

