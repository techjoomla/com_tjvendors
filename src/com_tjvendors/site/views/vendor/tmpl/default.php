<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
?>
<?php if (!empty($this->vendor_id) )
	{
	?>
	<h1>
		<?php
			echo Text::_('COM_TJVENDOR_VENDOR_PROFILE');
			?>
	</h1>
	<input type="hidden" name="client" value="<?php echo $this->input->get('client', '', 'STRING'); ?>" />
	<div class="profile row <?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>" id="tjv-wrapper">
		<div class="col-sm-12">
			<h3 class="mt-0">
				<?php echo htmlspecialchars($this->VendorDetail->vendor_title, ENT_COMPAT, 'UTF-8');?>
				<span class="pull-right">
					<small>
						<a  href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&&layout=profile&client=' .$this->input->get('client', '', 'STRING'). '&vendor_id=' . $this->vendor_id );?>">
						<i class="fa fa-wrench" aria-hidden="true"></i>  <?php echo Text::_("COM_TJVENDORS_VENDOR_UPDATE"); ?></a>
					</small>
				</span>
			</h3>
		</div>
		<?php
			if(!empty($this->VendorDetail->vendor_logo))
			{
			?>
				<div class="controls col-sm-3 center">
					<img  src="<?php echo Uri::root() . $this->VendorDetail->vendor_logo; ?>" width="100%">
				</div>
		<?php
			}
			else
			{
			?>
				<div class="controls col-sm-3 center">
					<img src="<?php echo Uri::root() . "/administrator/components/com_tjvendors/assets/images/default.png"; ?>" width="100%">
				</div>
		<?php
			}
			?>
				<div class="col-sm-9">
					<div>
						<div class='profile__content text-muted'>
						<?php echo $this->escape($this->VendorDetail->vendor_description);?>
						</div>
					</div>
				</div>
	</div>
	<?php
	}
	elseif(Factory::getUser()->id && !$this->vendor_id)
	{
		$app = Factory::getApplication();
		$client = $app->input->get('client', '', 'STRING');
		$link =Route::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $client);
		$app->enqueueMessage(Text::_('COM_TJVENDOR_REGISTRATION_VENDOR_ERROR'), 'warning');
		$app->redirect($link);
	}
	else
	{
		$link =Route::_('index.php?option=com_users');
		$app = Factory::getApplication();
		$app->redirect($link);
	}
	?>
<script>
	tjVAdmin.vendor.readMore();
</script>
