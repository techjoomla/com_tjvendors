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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

$lang = Factory::getLanguage();
$lang->load('plg_payment_paypal', JPATH_ADMINISTRATOR);
?>
<script type="text/javascript">
var _URL                              = window.URL || window.webkitURL;
var allowedMediaSizeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_SIZE_VALIDATE") . $this->params->get('image_size') . 'KB';?>";
var allowedImageDimensionErrorMessage = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_DIMENSIONS_VALIDATE");?>";
var allowedImageTypeErrorMessage      = "<?php echo Text::_("COM_TJVENDORS_VENDOR_LOGO_IMAGE_TYPE_VALIDATION");?>";
var vendor_id                         = '<?php echo $this->item->vendor_id;?>';
var client                            = '<?php echo $this->client;?>';
var layout                            = '<?php echo "update";?>';
const vendorAllowedMediaSize          = '<?php echo $max_images_size = $this->params->get('image_size') * 1024; ?>';
var country                           = "<?php echo $this->item->country; ?>";
var region                            = "<?php echo $this->item->region; ?>";
var city                              = "<?php echo $this->item->city; ?>";
tjVAdmin.vendor.initVendorJs();
var CommonObj = new tjvendor.UI.CommonUI();
</script>

<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&client='.$this->input->get('client', '', 'INTEGER').'&vendor_id=' . (int) $this->item->vendor_id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<div class="form-horizontal">
		<?php 
		echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'personal')); 
			echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'personal', Text::_('COM_TJVENDORS_TITLE_PERSONAL', true)); ?>
			<div class="row-fluid">
				<div class="span6 form-horizontal">
					<fieldset class="adminform">
						<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->item->vendor_id; ?>" />
						<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
						<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
						<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
						<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
						<input type="hidden" name="jform[vendor_client]" value="<?php echo $this->input->get('client', '', 'STRING'); ?>" />
						
						<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
						<input type="hidden" name="jform[modified_by]" value="<?php echo Factory::getUser()->id; ?>" />
						<input type="hidden" name="jform[created_time]" value="<?php echo $this->item->created_time; ?>" />
						<input type="hidden" name="jform[modified_time]" value="<?php echo $this->item->modified_time; ?>" />
							<?php
							echo $this->form->renderField('user_id');
							echo $this->form->renderField('client');
							echo $this->form->renderField('vendor_title');
							echo $this->form->renderField('alias');
							echo $this->form->renderField('state');
							echo $this->form->renderField('vendor_description');
							echo $this->form->renderField('vendor_logo');
							?>
							<div class="controls">
								<div class="alert alert-warning">
									<?php echo sprintf(Text::_("COM_TJVENDORS_FILE_UPLOAD_ALLOWED_EXTENSIONS"), 'jpg, jpeg, png');?>
								</div>
								<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->item->vendor_logo; ?>" />
								<?php if (!empty($this->item->vendor_logo)) : ?>
									<div class="control-group">
										<div><img src="<?php echo Uri::root() . $this->item->vendor_logo; ?>" class="span3 col-md-3 img-thumbnail pull-left marginb10 img-polaroid"></div>
									</div>
								<?php endif;?>
							</div>
					</fieldset>
				</div>
				<div class="span6 form-horizontal">
					<fieldset class="adminform">
						<?php
						echo $this->form->renderField('phone_number');
						echo $this->form->renderField('address');
						?>
						<div class="control-group" id="country_group">
							<div class="control-label">
								<label for="jform_country">
									<?php echo $this->form->getLabel('country'); ?>
								</label>
							</div>
							<div class="controls">
								<?php
									$default = null;

									if (isset($this->item->country))
									{
										$default = $this->item->country;
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

									if (empty($this->item->region))
									{
										$this->item->region = '';
										$this->item->city = '';
									}

									echo $this->dropdown = JHtml::_('select.genericlist', $options, 'jform[country]',
									'aria-invalid="false" size="1" onchange="CommonObj.generateStates(id,\'' .
									1 . '\',\'' . $this->item->region . '\',\'' . $this->item->city . '\')"', 'value', 'text', $default, 'jform_country');
								?>
							</div>
						</div>
						<div class="control-group" id="region_group">
							<div class="control-label">
								<label for="jform_region">
									<?php echo $this->form->getLabel('region'); ?>
								</label>
							</div>
							<div class="controls">
								<select name="jform[region]" id="jform_region"></select>
							</div>
						</div>
						<div class="control-group" id="city_group">
							<div class="control-label">
								<label for="jform_city">
									<?php echo $this->form->getLabel('city'); ?>
								</label>
							</div>
							<div class="controls">
								<select name="jform[city]" id="jform_city" onchange="CommonObj.showOtherCity('jform_city')"></select>
							</div>
						</div>
						<?php
						echo $this->form->renderField('other_city');
						echo $this->form->renderField('zip');
						echo $this->form->renderField('website_address');
						echo $this->form->renderField('vat_number');
						?>
					</fieldset>
				</div>
			</div>
		<?php 
			echo HTMLHelper::_('bootstrap.endTab'); 			
			echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'name', Text::_('COM_TJVENDORS_TITLE_PAYMENT_DETAILS')); 
				echo $this->form->getInput('payment_gateway');?>
				<div id="payment_details"></div>
				<input type="hidden" name="jform[primaryEmail]" id="jform_primaryEmail" value="0" />
		<?php 
			echo HTMLHelper::_('bootstrap.endTab'); 
		echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="client" value="<?php echo $this->input->get('client', '', 'STRING');?>"/>
		<input type="hidden" name="layout" value="<?php echo $this->input->get('layout', '', 'STRING');?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>

