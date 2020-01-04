<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');
?>
<script type="text/javascript">
	var layout = '<?php echo "profile";?>';
	var _URL                              = window.URL || window.webkitURL;
	var allowedMediaSizeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get('image_size') . 'KB';?>";
	var allowedImageDimensionErrorMessage = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE");?>";
	var allowedImageTypeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION");?>";
	const vendorAllowedMediaSize          = "<?php echo $max_images_size = $this->params->get('image_size') * 1024; ?>";
	tjVSite.vendor.initVendorJs();
</script>
<div id="tjv-wrapper" class="<?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>">
<?php
	if (Factory::getUser()->id )
	{
	?>
	<h1>
		<?php
			echo Text::_('COM_TJVENDOR_UPDATE_VENDOR');
			echo ':&nbsp' . htmlspecialchars($this->vendor->vendor_title, ENT_COMPAT, 'UTF-8');
			?>
	</h1>
	<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' . $this->input->get('vendor_id', '', 'INTEGER') . '&client=' . $this->input->get('client', '', 'STRING') ); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
		<div class="vendorForm">
			<div class="row">
				<div class="col-sm-12">
					<ul class="nav nav-tabs vendorForm__nav d-flex mb-15">
					  <li class="active"><a data-toggle="tab" href="#tab1"><?php echo Text::_('COM_TJVENDORS_TITLE_PERSONAL'); ?> </a></li>
					  <li><a data-toggle="tab" href="#tab2"><?php echo Text::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?></a></li>
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
								<input type="hidden" name="jform[approved]" value="<?php echo $this->vendorClientXrefTable->approved; ?>" />
								<div class="row">
									<div class="col-sm-6">
										<?php echo$this->form->renderField('vendor_title'); ?>
										<?php echo$this->form->renderField('alias'); ?>
										<?php echo $this->form->renderField('vendor_description'); ?>
									</div>
									<div class="col-sm-6">
									<?php
										if (!empty($this->vendor->vendor_logo))
										{
										?>
										<div class="form-group">
											<div class="row">
												<div class="col-xs-12 col-sm-10 col-md-7">
													<img class="img-responsive" src="<?php echo Uri::root() . $this->vendor->vendor_logo; ?>">
												</div>
											</div>
										</div>
									<?php
										}
										else
										{
										?>
										<div class="form-group">
											<div class="row">
												<div class="col-xs-12 col-sm-10 col-md-7">
													<img src="<?php echo Uri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>" class="img-thumbnail">
												</div>
											</div>
										</div>
									<?php
										}
										?>
										<div class="form-group">
											<?php echo $this->form->renderField('vendor_logo'); ?>
										</div>
										<div class="alert alert-info">
											<?php echo sprintf(Text::_("COM_TJVENDORS_MAXIMUM_LOGO_UPLOAD_SIZE_NOTE"), $this->params->get('image_size', '', 'STRING'));?>
										</div>
									</div>
								</div>
								<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->vendor->vendor_logo ?>" />
							</fieldset>
						</div>
						<!----Tab 1 End----->

						<!----Tab 2 Start----->
						<div id="tab2" class="tab-pane fade">
							<div class="row">
								<?php echo $this->form->getInput('payment_gateway');?>
						   </div>
						</div>
						<!----Tab 2 Start----->
					</div>
				<!----Tab Container End----->
				</div>
			</div>

			<div class="mt-10">
				<input type="hidden" name="task" value="vendor.save"/>
				<?php echo HTMLHelper::_('form.token'); ?>
				<button type="button" class="btn btn-default btn-primary" onclick="Joomla.submitbutton('vendor.save')">
					<span><?php echo Text::_('JSUBMIT'); ?></span>
				</button>

				<button class="btn btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
			</div>
		</div>
	</form>
<?php
	}
	else
	{
		$link = Route::_('index.php?option=com_users');
		$app = Factory::getApplication();
		$app->redirect($link);
	}
	?>
</div>
<script>
	/* Not Using this code*/
	/*tjVSite.vendor.tabToAccordion();*/
</script>
