<?php
/**
 * @package    TJ-Vendors
 * @author     TechJoomla <extensions@techjoomla.com>
 * @website    http://techjoomla.com
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	var layout = '<?php echo "edit";?>';
	var _URL                              = window.URL || window.webkitURL;
	var allowedMediaSizeErrorMessage      = "<?php echo JText::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get('image_size') . 'KB';?>";
	var allowedImageDimensionErrorMessage = "<?php echo JText::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE");?>";
	var allowedImageTypeErrorMessage      = "<?php echo JText::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION");?>";
	const vendorAllowedMediaSize          = "<?php echo $max_images_size = $this->params->get('image_size') * 1024; ?>";
	tjVSite.vendor.initVendorJs();
</script>
<div id="tjv-wrapper">
<?php
if (JFactory::getUser()->id )
{
	?>
	<h1>
		<?php echo JText::_('COM_TJVENDOR_CREATE_VENDOR');?>
	</h1>
	<form action="<?php echo JRoute::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' .$this->input->get('vendor_id', '', 'INTEGER') .'&client=' . $this->input->get('client', '', 'STRING') ); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="row">
			<div class="col-sm-12 vendorForm" id="tj-edit-form">
				<ul class="nav nav-tabs vendorForm__nav d-flex mb-15">
				  <li class="active"><a data-toggle="tab" href="#tab1"><?php echo JText::_('COM_TJVENDORS_TITLE_PERSONAL'); ?></a> </li>
				  <li><a data-toggle="tab" href="#tab2"><?php echo JText::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?></a></li>
				</ul>
				<!----Tab Container Start----->
				<div class="tab-content">
					<!----Tab 1 Start----->
					<div id="tab1" class="tab-pane fade in active">
						<fieldset class="adminform">
							<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->vendor_id; ?>" />
							<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->vendor->checked_out_time; ?>" />
							<input type="hidden" name="jform[checked_out]" value="<?php echo $this->vendor->checked_out; ?>" />
							<input type="hidden" name="jform[ordering]" value="<?php echo $this->vendor->ordering; ?>" />
							<input type="hidden" name="jform[state]" value="<?php echo $this->vendor->state; ?>" />
							<?php
							$input = JFactory::getApplication()->input;

							if ($this->vendor_id != 0)
							{
								$client = $this->input->get('client', '', 'STRING');
								?>
								<div class="pull-left alert alert-info">

									<?php
										echo JText::_('COM_TJVENDORS_DISPLAY_YOU_ARE_ALREADY_A_VENDOR_AS');
									?>

									<a href="<?php echo JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $client . '&vendor_id=' . $this->vendor_id);?>">
									<strong><?php echo $this->escape($this->VendorDetail->vendor_title);?></a></strong>

									<?php
									echo " " . JText::_('COM_TJVENDORS_DISPLAY_DO_YOU_WANT_TO_ADD');
									$tjvendorFrontHelper = new TjvendorFrontHelper;
									echo $client = $tjvendorFrontHelper->getClientName($client);
									echo JText::_('COM_TJVENDORS_DISPLAY_AS_A_CLIENT');
									?>
								</div>
								<input type="hidden" name="jform[vendor_client]" value="<?php echo $this->input->get('client', '', 'STRING'); ?>" />
								<input type="hidden" name="jform[vendor_title]" value="<?php echo $this->escape($this->VendorDetail->vendor_title); ?>" />
								<input type="hidden" name="jform[vendor_description]" value="<?php echo $this->escape($this->VendorDetail->vendor_description); ?>" />
								<?php
							}
							elseif($this->vendor_id == 0)
							{
							?>
							<div class="row">
								<div class="col-sm-6">
									<?php echo $this->form->renderField('vendor_title'); ?>
									<?php echo $this->form->renderField('alias'); ?>
									<?php echo $this->form->renderField('vendor_description'); ?>
								</div>
								<div class="col-sm-6">
									<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->vendor->vendor_logo ?>" />
									<?php
									if (!empty($this->vendor->vendor_logo))
									{
										?>
										<div class="form-group">
											<div class="row">
												<div class="col-xs-12 col-sm-10 col-md-7">
													<img src="<?php echo JUri::root() . $this->vendor->vendor_logo; ?>">
												</div>
											</div>
										</div>
										<?php
									}

									if (empty($this->vendor->vendor_logo))
									{
									?>
										<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="/administrator/components/com_tjvendors/assets/images/default.png" />
										<div class="form-group">
											<div class="row">
												<div class="col-xs-12 col-sm-10 col-md-7">
													<img src="<?php echo JUri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>">
												</div>
											</div>
											<div class="mt-10">
												<?php echo $this->form->renderField('vendor_logo'); ?>
											</div>
										</div>
										<?php
									}
									?>
									<div class="alert alert-info">
										<?php echo sprintf(JText::_("COM_TJVENDORS_MAXIMUM_LOGO_UPLOAD_SIZE_NOTE"), $this->params->get('image_size', '', 'STRING'));?>
									</div>
								</div>
							</div>
							<?php
							}
							?>
						</fieldset>
					</div>
					<!----Tab 1 End----->

					<!----Tab 2 Start----->
					<div id="tab2" class="tab-pane fade">
						<div class="row">
							<?php echo $this->form->getInput('payment_gateway');?>
						</div>
					</div>
					<!----Tab 2 End----->
				</div>
				<!----Tab Container End----->
			</div>
		</div>

		<?php
		if ($this->vendor_id == 0)
		{
			?>
			<div class="mt-10">
				<button type="button" class="btn btn-default btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
					<span><?php echo JText::_('JSUBMIT'); ?></span>
				</button>

				<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo JText::_('JCANCEL'); ?>
				</button>
			</div>
		<?php
		}
		else
		{
			?>
			<div class="mt-10">
				<button type="button" class="btn btn-default  btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
					<span><?php echo JText::_('COM_TJVENDORS_CLIENT_APPROVAL'); ?></span>
				</button>
				<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo JText::_('COM_TJVENDORS_CLIENT_REJECTION'); ?>
				</button>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="task" value="vendor.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
	<?php
}
else
{
	$link = JRoute::_('index.php?option=com_users');
	$app = JFactory::getApplication();
	$app->redirect($link);
}
?>
</div>
<script>
	/* Not Using this code*/
	/*tjVSite.vendor.tabToAccordion();*/
</script>
