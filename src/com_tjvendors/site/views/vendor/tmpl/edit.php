<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

/** @var $this TjvendorsViewVendor */

?>
<script type="text/javascript">
	var layout = '<?php echo "edit";?>';
	var _URL                              = window.URL || window.webkitURL;
	var allowedMediaSizeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get('image_size') . 'KB';?>";
	var allowedImageDimensionErrorMessage = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE");?>";
	var allowedImageTypeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION");?>";
	const vendorAllowedMediaSize          = "<?php echo $this->params->get('image_size') * 1024; ?>";
	tjVSite.vendor.initVendorJs();
</script>
<div id="tjv-wrapper" class="<?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>">
	<h1>
		<?php echo Text::_('COM_TJVENDOR_CREATE_VENDOR');?>
	</h1>
	<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&vendor_id=' .$this->vendor->vendor_id .'&client=' . $this->client); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="row">
			<div class="col-sm-12 vendorForm" id="tj-edit-form">
				<?php if(!$this->vendor->isAssociatedToClient($this->client)){ ?>
				<ul class="nav nav-tabs vendorForm__nav d-flex mb-15">
					<li class="active"><a data-toggle="tab" href="#tab1"><?php echo Text::_('COM_TJVENDORS_TITLE_PERSONAL'); ?></a> </li>
					<li><a data-toggle="tab" href="#tab2"><?php echo Text::_('COM_TJVENDORS_VENDOR_PAYMENT_GATEWAY_DETAILS'); ?></a></li>
				</ul>
				<?php } ?>
				<div class="tab-content">
					<div id="tab1" class="tab-pane fade in active">
						<fieldset class="adminform">
							<?php
								if ($this->vendor->vendor_id)
								{
									?>
							<div class="pull-left alert alert-info">
								<?php
									echo Text::_('COM_TJVENDORS_DISPLAY_YOU_ARE_ALREADY_A_VENDOR_AS');
									?>
								<a href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $this->client . '&vendor_id=' . $this->vendor->vendor_id);?>">
								<strong><?php echo $this->escape($this->vendor->getTitle());?></strong></a>
								<?php
								if (!$this->vendor->isAssociatedToClient($this->client))
									{
										echo " " . Text::_('COM_TJVENDORS_DISPLAY_DO_YOU_WANT_TO_ADD');
										$tjvendorFrontHelper = new TjvendorFrontHelper;
										echo $tjvendorFrontHelper->getClientName($this->client);
										echo Text::_('COM_TJVENDORS_DISPLAY_AS_A_CLIENT');
									}
									?>
							</div>
							<input type="hidden" name="jform[vendor_client]" value="<?php echo $this->client; ?>" />
							<input type="hidden" name="jform[vendor_title]" value="<?php echo $this->form->getValue('vendor_title');; ?>" />
							<input type="hidden" name="jform[vendor_description]" value="<?php echo $this->form->getValue('vendor_description'); ?>" />
							<?php
								}
								elseif(!$this->vendor->vendor_id)
								{
								?>
							<div class="row">
								<div class="col-sm-6">
									<?php echo $this->form->renderField('vendor_title'); ?>
									<?php echo $this->form->renderField('alias'); ?>
									<?php echo $this->form->renderField('vendor_description'); ?>
								</div>
								<div class="col-sm-6">

									<div class="form-group">
										<div class="row">
											<div class="col-xs-12 col-sm-10 col-md-7">
												<img src="<?php echo $this->vendor->getLogo(); ?>">
											</div>
										</div>
										<div class="mt-10">
											<?php echo $this->form->renderField('vendor_logo'); ?>
										</div>
									</div>

									<?php
										if (!empty($this->vendor->vendor_logo))
										{
											?>
											<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->form->getValue('vendor_logo'); ?>" />
									<?php
										}
										else
										{
										?>
											<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="/administrator/components/com_tjvendors/assets/images/default.png" />
									<?php
										}
										?>
									<div class="alert alert-info">
										<?php echo sprintf(Text::_("COM_TJVENDORS_MAXIMUM_LOGO_UPLOAD_SIZE_NOTE"), $this->params->get('image_size', '', 'STRING'));?>
									</div>
								</div>
							</div>
							<?php
								}
								?>

								<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->form->getValue('vendor_id');?>" />
								<?php echo $this->form->renderField('checked_out_time'); ?>
								<?php echo $this->form->renderField('checked_out'); ?>
								<?php echo $this->form->renderField('ordering'); ?>
								<input type="hidden" name="jform[approved]" value="<?php echo $this->form->getValue('approved');?>" />
								<input type="hidden" name="jform[state]" value="<?php echo $this->form->getValue('state');?>" />

						</fieldset>
					</div>
					<div id="tab2" class="tab-pane fade">
						<div class="row">
							<?php echo $this->form->getInput('payment_gateway');?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			if ($this->vendor->vendor_id == 0)
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
			elseif (!$this->vendor->isAssociatedToClient($this->client))
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
</div>

