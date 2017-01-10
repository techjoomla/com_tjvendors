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
		 if (task == 'vendor.cancel')
		{
			Joomla.submitform(task, document.getElementById('vendor-form'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('vendor-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' . (int) $this->vendor->vendor_id  . '&client=' . $this->vendor->vendor_client); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="vendor-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->vendor->vendor_id; ?>" />
						<?php echo $this->form->renderField('vendor_title'); ?>
						<?php echo $this->form->renderField('vendor_description'); ?>
						<?php echo $this->form->renderField('vendor_logo'); ?>
						<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->vendor->vendor_logo ?>" />

						<?php if (!empty($this->vendor->vendor_logo)) : ?>
							<div class="control-group">
								<div class="controls "><img src="<?php echo JUri::root() . $this->vendor->vendor_logo; ?>"></div>
							</div>
						<?php endif; ?>
				</fieldset>
			</div>
		</div>
			<div>
				<button type="button" class="btn btn-default  btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
					<span><?php echo JText::_('JSUBMIT'); ?></span>
				</button>

				<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo JText::_('JCANCEL'); ?>
				</button>
			</div>

		<?php echo $this->form->renderField('vendor_client'); ?>
		<?php echo $this->form->renderField('user_id'); ?>
		<?php echo $this->form->renderField('vendor_id'); ?>
		<input type="hidden" name="task" value="vendor.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

