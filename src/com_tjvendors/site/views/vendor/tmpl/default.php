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
<div class="vendor-cover row-fluid">
	<div class="span3">
	<img alt="" src="<?php echo JUri::root() . $this->vendor->vendor_logo; ?>">
	</div>
	<div class="span9">
		<div><h3><?php echo $this->vendor->vendor_title; ?></h3></div>
		<div><?php echo $this->vendor->vendor_description; ?></div>
		<div>
		<?php if ($this->vendor->user_id == JFactory::getUser()->id || JFactory::getUser()->authorise('core.admin')){?>
		<span class="vendor-action pull-right"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $this->vendor->vendor_client . '&vendor_id=' . $this->vendor->vendor_id );?>"><?php echo JText::_("COM_TJVENDORS_VENDOR_UPDATE"); ?></a></span>
		<?php } ?>
		</div>
	</div>
</div>
