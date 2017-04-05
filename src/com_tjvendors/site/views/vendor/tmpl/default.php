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
?>
<?php if (JFactory::getUser()->id && !empty($this->vendor_id) ){?>

	<input type="hidden" name="client" value="<?php echo $this->input->get('client', '', 'STRING'); ?>" />
<div class="vendor-cover row-fluid">
	<div class="span3">
	<img width="200px" alt="" src="<?php echo JUri::root() . $this->VendorDetail->vendor_logo; ?>">
	</div>
	<div class="span9">
		<div>
			<h3>
				<?php echo $this->VendorDetail->vendor_title; ?>
			</h3>
		</div>
		<div>
			<?php echo $this->VendorDetail->vendor_description; ?>
		</div>

	</div>
</div>
<div class="vendor-cover row-fluid">
		<div class="span6">
			<span class="vendor-action pull-right"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_tjvendors&view=vendor&&layout=profile&client=' .$this->input->get('client', '', 'STRING'). '&vendor_id=' . $this->vendor_id );?>"><?php echo JText::_("COM_TJVENDORS_VENDOR_UPDATE"); ?></a></span>
		</div>
</div>
<?php }
else
{
	$link =JRoute::_('index.php?option=com_users');
	$app = JFactory::getApplication();
	$app->redirect($link);
} ?>
