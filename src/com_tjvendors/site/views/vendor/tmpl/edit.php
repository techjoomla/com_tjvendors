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
	var layout = '<?php echo "edit";?>';
	let CommonObj = new tjvendor.UI.CommonUI();
	var _URL                              = window.URL || window.webkitURL;
	var allowedMediaSizeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get('image_size') . 'KB';?>";
	var allowedImageDimensionErrorMessage = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE");?>";
	var allowedImageTypeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION");?>";
	const vendorAllowedMediaSize          = "<?php echo $max_images_size = $this->params->get('image_size') * 1024; ?>";
	var country   = "<?php echo $this->vendor->country; ?>";
	var region    = "<?php echo $this->vendor->region; ?>";
	var city      = "<?php echo $this->vendor->city; ?>";
	tjVSite.vendor.initVendorJs();
</script>

<div id="tjv-wrapper" class="<?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>">
<?php
if (Factory::getUser()->id)
{
	?>
	<h1>
		<?php echo Text::_('COM_TJVENDOR_CREATE_VENDOR');?>
	</h1>
	<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' . $this->input->get('vendor_id', '', 'INTEGER') . '&client=' . $this->input->get('client', '', 'STRING') ); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="row">
			<div class="col-sm-12 vendorForm" id="tj-edit-form">
			<?php
				if (!$this->isClientExist)
				{
					?>
					<ul class="nav nav-tabs vendorForm__nav d-flex mb-15">
						<li class="active"><a data-toggle="tab" href="#tab1"><?php echo Text::_('COM_TJVENDORS_TITLE_PERSONAL'); ?></a> </li>
						<li><a data-toggle="tab" href="#tab2"><?php echo Text::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?></a></li>
					</ul>
			<?php
				} ?>
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
							<input type="hidden" name="jform[created_by]" value="<?php echo Factory::getUser()->id;?>" />
							<input type="hidden" name="jform[modified_by]" value="0" />
							<input type="hidden" name="jform[created_time]" value="<?php echo $this->vendor->created_time; ?>" />
							<input type="hidden" name="jform[modified_time]" value="<?php echo $this->vendor->modified_time; ?>" />
							<?php
							$input = Factory::getApplication()->input;

							if ($this->vendor_id != 0)
							{
								?>
								<div class="pull-left alert alert-info">
									<?php echo Text::_('COM_TJVENDORS_DISPLAY_YOU_ARE_ALREADY_A_VENDOR_AS');?>

									<a href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $this->client . '&vendor_id=' . $this->vendor_id);?>">
									<strong><?php echo $this->escape($this->VendorDetail->vendor_title);?></a></strong>

									<?php
									if (!$this->isClientExist)
									{
										echo " " . Text::_('COM_TJVENDORS_DISPLAY_DO_YOU_WANT_TO_ADD');
										$tjvendorFrontHelper = new TjvendorFrontHelper;
										echo $client = $tjvendorFrontHelper->getClientName($this->client);
										echo Text::_('COM_TJVENDORS_DISPLAY_AS_A_CLIENT');
									}
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
									<?php 
										echo $this->form->renderField('vendor_title');
										echo $this->form->renderField('alias');
										echo $this->form->renderField('vendor_description');
										echo $this->form->renderField('address');
										echo $this->form->renderField('phone_number');
										echo $this->form->renderField('website_address');
										echo $this->form->renderField('vat_number');													
									?>
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
													<img src="<?php echo Uri::root() . $this->vendor->vendor_logo; ?>">
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
													<img src="<?php echo Uri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>">
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
										<?php echo sprintf(Text::_("COM_TJVENDORS_MAXIMUM_LOGO_UPLOAD_SIZE_NOTE"), $this->params->get('image_size', '', 'STRING'));?>
									</div>
									
									<div class="control-group" id="country_group">
										<div class="control-label">
											<label for="jform_country">
												<?php echo $this->form->getLabel('country'); ?>
											</label>
										</div>
										<div class="controls">
											<?php
												$default = null;

												if (isset($this->vendor->country))
												{
													$default = $this->vendor->country;
												}

												$options = array();
												$options[] = JHtml::_('select.option', 0, JText::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION'));

												foreach ($this->countries as $key => $value)
												{
													$country = $this->countries[$key];
													$id = $country['id'];
													$value = $country['country'];
													$options[] = JHtml::_('select.option', $id, $value);
												}
										
												if ($this->vendor->region == null)
												{
													$this->vendor->region = '';
													$this->vendor->city = '';
												}
										
												echo $this->dropdown = JHtml::_('select.genericlist', $options, 'jform[country]',
												'aria-invalid="false" size="1" onchange="CommonObj.generateStates(id,\'' .
												0 . '\',\'' . $this->vendor->region . '\',\'' . $this->vendor->city . '\')"', 'value', 'text', $default, 'jform_country');
											?>
										</div>
									</div>	
									<?php
										echo $this->form->renderField('region');
										echo $this->form->renderField('city');
										echo $this->form->renderField('other_city');
										echo $this->form->renderField('zip');
									?>								
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
					<span><?php echo Text::_('JSUBMIT'); ?></span>
				</button>

				<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
			</div>
		<?php
		}
		elseif (!$this->isClientExist)
		{
			?>
			<div class="mt-10">
				<button type="button" class="btn btn-default  btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
					<span><?php echo Text::_('COM_TJVENDORS_CLIENT_APPROVAL'); ?></span>
				</button>
				<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
					<?php echo Text::_('COM_TJVENDORS_CLIENT_REJECTION'); ?>
				</button>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="task" value="vendor.save"/>
		<?php echo HTMLHelper::_('form.token'); ?>
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
