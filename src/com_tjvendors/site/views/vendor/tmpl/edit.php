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
HTMLHelper::script(Uri::root() . 'libraries/techjoomla/assets/js/tjvalidator.js');

$script   = array();
$script[] = 'var layout = "edit"';
$script[] = 'let CommonObj = new tjvendor.UI.CommonUI();';
$script[] = 'var _URL = window.URL || window.webkitURL;';
$script[] = 'var allowedMediaSizeErrorMessage = "' . Text::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get("image_size") . "KB" . '"';
$script[] = 'var allowedImageDimensionErrorMessage = "' . Text::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE") . '"';
$script[] = 'var allowedImageTypeErrorMessage = "' . Text::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION") . '"';
$script[] = 'const vendorAllowedMediaSize = "' . $max_images_size = $this->params->get("image_size") * 1024 . '"';
$script[] = 'var country = "' . $this->vendor->country . '"';
$script[] = 'var region = "' . $this->vendor->region . '"';
$script[] = 'var city   = "' . $this->vendor->city . '"';
$script[] = 'var gatewayName = "' . $this->gateways[0] . '"'; 
$script[] = 'var gatewayCount = "' . $this->gatewayCount . '"'; 
$script[] = 'tjVSite.vendor.initVendorJs();';

Factory::getDocument()->addScriptDeclaration(implode("\n", $script));
?>
<div id="tjv-wrapper" class="<?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>">
<?php
if (Factory::getUser()->id)
{
	?>
	<h1 fs-title mt-10>
		<strong>
		<?php 
			if ($this->vendor_id)
			{
				echo Text::_('COM_TJVENDOR_UPDATE_VENDOR');
				echo ':&nbsp' . htmlspecialchars($this->vendor->vendor_title, ENT_COMPAT, 'UTF-8');
			}
			else
			{
				echo Text::_('COM_TJVENDOR_CREATE_VENDOR');
			}
		?>
		</strong>
	</h1>
	<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' . $this->input->get('vendor_id', '', 'INTEGER') . '&client=' . $this->input->get('client', '', 'STRING')); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="row">
			<div class="col-xs-12 vendorForm" id="tj-edit-form">
				<div class="col-xs-12 col-md-5">
							<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->vendor_id; ?>" />
							<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->vendor->checked_out_time; ?>" />
							<input type="hidden" name="jform[checked_out]" value="<?php echo $this->vendor->checked_out; ?>" />
							<input type="hidden" name="jform[ordering]" value="<?php echo $this->vendor->ordering; ?>" />
							<input type="hidden" name="jform[state]" value="<?php echo $this->vendor->state; ?>" />					
							<input type="hidden" name="jform[created_by]" value="<?php echo Factory::getUser()->id;?>" />
							<input type="hidden" name="jform[modified_by]" 
							value="<?php echo (isset($this->vendor_id)) ? Factory::getUser()->id : '0';?>" />
							<input type="hidden" name="jform[created_time]" value="<?php echo $this->vendor->created_time; ?>" />
							<input type="hidden" name="jform[modified_time]" value="<?php echo $this->vendor->modified_time; ?>" />
							<?php
							$input = Factory::getApplication()->input;

							if (!empty($this->vendor->vendor_logo))
							{
								$this->vendorLogoProfileImg = $this->vendor->vendor_logo;
								$this->vendorLogoProfileImgPath = Uri::root() . $this->vendorLogoProfileImg;
							}
							?>
							<div class="row">
						<div class="col-xs-12 col-sm-6">
									<div class="form-group">
								<?php echo $this->form->getLabel('vendor_title'); ?>
								<?php echo $this->form->getInput('vendor_title'); ?>
							</div>
						</div>
						<?php if (isset($this->alias_config) == 0)
						{ ?>
						<div class="col-xs-12 col-sm-6 pr-10">
							<div class="form-group">
								<?php echo $this->form->getLabel('alias'); ?>
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
						<?php
						} ?>
						<?php if (isset($this->phone_number_config) == 0)
						{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('phone_number'); ?>
								<?php echo $this->form->getInput('phone_number'); ?>
											</div>
										</div>
						<?php
						}
						if (isset($this->website_address_config) == 0)
						{ ?>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<?php echo $this->form->getLabel('website_address'); ?>
									<?php echo $this->form->getInput('website_address'); ?>
								</div>
							</div>
						<?php
						}
						if (isset($this->vat_number_config) == 0)
						{ ?>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<?php echo $this->form->getLabel('vat_number'); ?>
									<?php echo $this->form->getInput('vat_number'); ?>
								</div>
							</div>
						<?php
						}
						if (isset($this->address_config) == 0)
						{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('address'); ?>
								<?php echo $this->form->getInput('address'); ?>
										</div>
									</div>
						<?php
						} ?>
						<?php
						if (isset($this->country_config) == 0)
						{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group" id="country_group">
										<div class="control-label">
											<label for="jform_country">
												<?php echo $this->form->getLabel('country'); ?>
											</label>
										</div>
										<div class="controls">
											<?php echo $this->dropdown = JHtml::_('select.genericlist', $this->options, 'jform[country]',
												'aria-invalid="false" size="1" onchange="CommonObj.generateStates(id,\'' .
												0 . '\',\'' . $this->vendor->region . '\',\'' . $this->vendor->city . '\')"', 'value', 'text', $this->default, 'jform_country');
											?>
										</div>
									</div>	
								</div>
						<?php
						}
						if (isset($this->state_config) == 0)
						{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('region'); ?>
								<?php echo $this->form->getInput('region'); ?>
							</div>
					</div>
						<?php
						}?>
					<?php
					if (isset($this->city_config) == 0)
					{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('city'); ?>
								<?php echo $this->form->getInput('city'); ?>
						</div>
					</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('other_city'); ?>
								<?php echo $this->form->getInput('other_city'); ?>
				</div>
						</div>
					<?php
					}
					if (isset($this->zip_config) == 0)
					{ ?>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<?php echo $this->form->getLabel('zip'); ?>
								<?php echo $this->form->getInput('zip'); ?>
							</div>
						</div>
					<?php
					}?>
					</div>
				</div>
				<div class="col-xs-12 col-md-5 col-md-offset-2">
					<div class="row">
					<?php
					if (isset($this->vendor_logo_config) == 0)
					{ ?>
						<div class="col-sm-4 col-md-6 col-xs-12">
							<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->vendorLogoProfileImg ?>" />
							<div class="form-group">
								<img src="<?php echo $this->vendorLogoProfileImgPath; ?>">
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<?php echo $this->form->renderField('vendor_logo'); ?>
						</div>
						<div class="col-xs-12">
							<div class="alert alert-info">
								<p>
									<?php echo sprintf(Text::_("COM_TJVENDORS_MAXIMUM_LOGO_UPLOAD_SIZE_NOTE"), $this->params->get('image_size', '', 'STRING'));?>
								</p>
							</div>
						</div>
					<?php
					}
					if (isset($this->vendor_description_config) == 0)
					{ ?>
						<!--Description-->
						<div class="col-xs-12">
							<div class="form-group">
								<?php echo $this->form->getLabel('vendor_description'); ?>
								<?php echo $this->form->getInput('vendor_description'); ?>
							</div>
						</div>
					<?php
					} ?>
				</div>
			</div>
		</div>
		</div>
		<?php
		if (isset($this->payment_gateway_config) == 0)
		{ ?>
		<div class="row">
				<div class="alert alert-info">
					<p>
						<?php echo Text::_("COM_TJVENDORS_VENDOR_PAYMENT_DETAILS_NOTE");?>
					</p>
				</div>
			<div class="col-xs-12">
				<?php echo $this->form->getInput('payment_gateway');?>
			</div>
		</div>
		<?php
		}
		?>
		<div class="row">
		<div class="mt-10">
			<button type="button" class="btn btn-default btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
				<span><?php echo Text::_('JSUBMIT'); ?></span>
			</button>
			<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
				<?php echo (!$this->isClientExist) ? Text::_('COM_TJVENDORS_CLIENT_REJECTION') : Text::_('JCANCEL'); ?>
			</button>
		</div>
		</div>
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
	if ($this->gatewayCount == 1)
	{
		?>
		<style>
			.subform-repeatable-group > div:first-child {
				display: none;
			}
		</style>
		<?php
}
?>
</div>
