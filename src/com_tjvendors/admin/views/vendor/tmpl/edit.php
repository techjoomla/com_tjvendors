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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
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
const vendorAllowedMediaSize          = '<?php echo $max_images_size = $this->params->get('image_size') * 1024; ?>';
var vendor_id                         = '<?php echo $this->item->vendor_id;?>';
var client                            = '<?php echo $this->client;?>';
var layout                            = '<?php echo "default";?>';
tjVAdmin.vendor.initVendorJs();
</script>

<form action="<?php echo Route::_('index.php?option=com_tjvendors&layout=edit&client='.$this->input->get('client', '', 'INTEGER').'&vendor_id=' . (int) $this->item->vendor_id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'personal')); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'personal', Text::_('COM_TJVENDORS_TITLE_PERSONAL', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[vendor_id]" value="<?php echo $this->item->vendor_id; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[vendor_client]" value="<?php echo $this->input->get('client', '', 'STRING'); ?>" />

					<?php
						if ($this->item->vendor_id != 0)
						{
							$input=Factory::getApplication()->input;
							$client=$input->get('client', '', 'STRING');
						?>
							<input type="hidden" name="jform[vendor_title]" id="jform_vendor_titile_hidden" value="<?php echo $this->item->vendor_title; ?>" />
							<input type="hidden" name="jform[vendor_description]" id="jform_vendor_description_hidden" value="<?php echo $this->item->vendor_description; ?>" />
							<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="<?php echo $this->item->vendor_logo; ?>" />
						<?php echo $this->form->renderField('user_id');?>
							<div class="pull-left alert alert-info">
						<?php echo Text::_('COM_TJVENDORS_DISPLAY_YOU_ARE_ALREADY_A_VENDOR_AS');?>
							<a href="<?php echo Route::_(JURI::root() . '/administrator/index.php?option=com_tjvendors&view=vendor&layout=update&client=' . $client . '&vendor_id='.$this->item->vendor_id);?>"><strong>
							<?php
								echo $this->item->vendor_title."</a></strong>";
								echo " ".Text::_('COM_TJVENDORS_DISPLAY_DO_YOU_WANT_TO_ADD');
								$tjvendorFrontHelper = new TjvendorFrontHelper();
								echo $clientTitle = $tjvendorFrontHelper->getClientName($client);
								echo Text::_('COM_TJVENDORS_DISPLAY_AS_A_CLIENT');
							?>
							</div>
					<?php }
						elseif($this->item->vendor_id==0)
						{
							echo $this->form->renderField('user_id');
							echo $this->form->renderField('client');
							echo $this->form->renderField('vendor_title');
							echo $this->form->renderField('alias');
							echo $this->form->renderField('state');
							echo $this->form->renderField('vendor_description');
							echo $this->form->renderField('vendor_logo');
							if(empty($this->item->vendor_logo)) :?>
								<input type="hidden" name="jform[vendor_logo]" id="jform_vendor_logo_hidden" value="/administrator/components/com_tjvendors/assets/images/default.png" />
									<div class="control-group">
											<div class="controls "><img src="<?php echo Uri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>" class="span3 col-md-3 img-thumbnail marginb10 img-polaroid"></div>
										</div>

							<?php endif;
							?>
							<div class="controls">
							<div class="alert alert-warning">
							<?php
							echo sprintf(Text::_("COM_TJVENDORS_FILE_UPLOAD_ALLOWED_EXTENSIONS"), 'jpg, jpeg, png');
							?>
							</div>
							<?php
						}
						?>
				</fieldset>
			</div>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'name', Text::_('COM_TJVENDORS_TITLE_PAYMENT_DETAILS')); ?>
				<?php echo $this->form->getInput('payment_gateway'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php
			if($this->item->vendor_id != 0)
			{
			?>
				<div>
					<button type="button" class="btn btn-default  btn-primary"  onclick="Joomla.submitbutton('vendor.save')">
						<span><?php echo Text::_('JSUBMIT'); ?></span>
					</button>
					<button class="btn  btn-default" onclick="Joomla.submitbutton('vendor.cancel')">
						<span><?php echo Text::_('JCANCEL'); ?></span>
					</button>
				</div>
			<?php
			}
		?>
		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="client" value="<?php echo $this->input->get('client', '', 'STRING');?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>

