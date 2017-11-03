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
	var layout = '<?php echo "profile";?>';
	tjVSite.vendor.initVendorJs();
</script>
<div id="tvwrap">
<?php
if (JFactory::getUser()->id ){?>
	<h2>
		<?php
		   echo JText::_('COM_TJVENDOR_UPDATE_VENDOR');
		   echo ':&nbsp' . $this->vendor->vendor_title;
	     ?>
	</h2>
<form action="<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' .$this->input->get('vendor_id', '', 'INTEGER') .'&client=' . $this->input->get('client', '', 'STRING') ); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="vendor-form">
	<div class="vendor-form">
		<div class="row">
			<div class="col-sm-12">
				<ul class="tabs mb-15 hidden-xs">
				  <li class="active" rel="tab1"><a><?php echo JText::_('COM_TJVENDORS_TITLE_PERSONAL'); ?> </a></li>
				  <li rel="tab2"><?php echo JText::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?></li>
				</ul>
				<div class="tab__container">
					  <h4 class="tab_active tab__heading visible-xs" rel="tab1"><?php echo JText::_('COM_TJVENDORS_TITLE_PERSONAL'); ?><i class="fa fa-angle-double-down pull-right" aria-hidden="true"></i></h4>
					  <div id="tab1" class="tab__content">
						<fieldset class="adminform">
							<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->vendor_id; ?>" />
							<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->vendor->checked_out_time; ?>" />
							<input type="hidden" name="jform[checked_out]" value="<?php echo $this->vendor->checked_out; ?>" />
							<input type="hidden" name="jform[ordering]" value="<?php echo $this->vendor->ordering; ?>" />
							<input type="hidden" name="jform[state]" value="<?php echo $this->vendor->state; ?>" />
							<div class="row">
								<div class="col-sm-6">
									<?php echo$this->form->renderField('vendor_title'); ?>
									<?php echo$this->form->renderField('alias'); ?>
									<?php echo $this->form->renderField('vendor_description'); ?>
								</div>
								<div class="col-sm-6">
									<?php if (!empty($this->vendor->vendor_logo))
									{ ?>
										<div class="form-group">
											<div class="controls">
												<img class="img-responsive" src="<?php echo JUri::root() . $this->vendor->vendor_logo; ?>">
											</div>
										</div>
								<?php }
									else
									{
										?>
										<div class="form-group">
											<div class="controls">
												<img src="<?php echo JUri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>" class="img-thumbnail">
											</div>
										</div>
										<?php
									}
								 ?>
								 <div class="form-group">
									<?php echo $this->form->renderField('vendor_logo'); ?>
								 </div>
								 	
								</div>
							</div>								
							<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->vendor->vendor_logo ?>" />

						</fieldset>
					</div>
					<h4 class="tab__heading visible-xs" rel="tab2"><?php echo JText::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?><i class="fa fa-angle-double-down pull-right" aria-hidden="true"></i></h4>
					<div id="tab2" class="tab__content">
						<div class="row">
							<div class="form-group col-sm-6 col-md-4">
								<?php echo $this->form->renderField('payment_gateway');?>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-md-4" id="payment_details">
							</div>
					   </div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="mt-10">
			<input type="hidden" name="task" value="vendor.save"/>
			<?php echo JHtml::_('form.token'); ?>
			<button type="button" class="btn btn-default btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
				<span><?php echo JText::_('JSUBMIT'); ?></span>
			</button>

			<button class="btn btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
		</div>
	</div>
	</form>
</div>
<?php }
else
{
	$link =JRoute::_('index.php?option=com_users');
	$app = JFactory::getApplication();
	$app->redirect($link);
}
?>
<script>
	tjVSite.vendor.tabToAccordion();
</script>
