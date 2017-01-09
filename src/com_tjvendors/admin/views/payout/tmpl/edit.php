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
		if (task == 'payout.cancel')
		{
			Joomla.submitform(task, document.getElementById('payout-form'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('payout-form'));
		}
	}
	/*$(document).ready(function(){
    $("#pay-confirmation").click(function(e){
		var txt ="yes or no";
	
		if(confirm(txt)==true){
			alert("yes");
			return true;
		}
		alert("here");
		e.preventDefault();
		return false;
        
    });
}); 
*/
function Popup()
{
	var txt;
	var r = confirm("Press a button!");
	
	if (r == true) 
	{
		txt = "You pressed OK!";
	} 
	else 
    {
		return false;
	}
}

</script>
<form action="<?php //echo JRoute::_('index.php?option=com_tjvendors&layout=edit&payout_id=' . (int) $this->item->payout_id ); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="payout-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TJVENDORS_TITLE_PAYOUT', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->payout_id; ?>" />
						
					
						<?php echo $this->form->renderField('vendor_id'); ?>
						<?php echo $this->form->renderField('currency'); ?>
						<?php echo $this->form->renderField('total'); ?>
						<button class="validate btn btn-primary" id="pay-confirmation" onclick="return Popup()" >PAY</button>
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
		<input type="hidden" name="task" value="payout.save"/>
		<input type="hidden" name="pendingamount" value="<?php echo $this->item->total;?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

