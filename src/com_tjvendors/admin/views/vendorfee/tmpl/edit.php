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


?>
<script type="text/javascript">

	Joomla.submitbutton = function (task)
	{
		if(task == 'vendorfee.apply' || task == 'vendorfee.save')
		{
				var percent_commission = document.getElementById("jform_percent_commission").value;
				if (percent_commission > 100)
				{
						alert("<?php echo JText::_('COM_TJVENDORS_FEES_PERCENT_ERROR_DESC');?>");
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

<form action="
<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&id=' . (int) $this->id . '&vendor_id=' . (int) $this->item->vendor_id);?>"
method="post" enctype="multipart/form-data" name="adminform" id="vendorfee-form" class="form-validate">

	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

						<?php echo $this->form->renderField('vendor_title'); ?>

						<?php
						if (empty($this->item->currency))
						{
							?>
						<div class = "control-group" >
							<div class = "control-label">
						<label class = "hasPopover" title data-content = "Currency"><?php echo $this->form->getLabel('currency');?></label>
						</div>
						<div class = "controls">
							
						<input type="text" name="currency" value="<?php echo $this->curr; ?>" readonly="true"></input>

						</div>
						</div>
						<?php
						}
						else
						{
							echo $this->form->renderField('currency');
						}
						?>
						<?php echo $this->form->renderField('percent_commission'); ?>
						<?php echo $this->form->renderField('flat_commission'); ?>

				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="jform[currency]" value="<?php echo $this->curr;?>" />
		<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->vendor_id;?>" />
		<input type="hidden" name="jform[id]" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="jform[client]" value="<?php echo $this->item->client; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

