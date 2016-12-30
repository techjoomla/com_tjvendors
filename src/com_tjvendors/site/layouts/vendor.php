<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$lang = JFactory::getLanguage();
$lang->load('com_tjvendors', JPATH_SITE, $lang->getTag(), true);

defined('JPATH_BASE') or die;
?>
<div class="vendor-cover row-fluid">
	<div class="span3">
	<img alt="" src="<?php echo JUri::root() . $displayData['vendor_logo']; ?>">
</div>
	<div class="span9">
		<div><h3><?php echo $displayData['vendor_title']; ?></h3>
		<?php if ($displayData['user_id'] == JFactory::getUser()->id || JFactory::getUser()->authorise('core.admin')){?>
		<span class="vendor-action pull-right"><a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&vendor_id=' . (int) $displayData['vendor_id']  . '&client=' . $displayData['vendor_client']); ?>"><?php echo JText::_("COM_TJVENDORS_VENDOR_UPDATE"); ?></a></span>
		<?php } ?>
		</div>
		<div><?php echo $displayData['vendor_description']; ?></div>
	</div>
</div>
