<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var $this TjvendorsViewVendor */
?>
<h1>
	<?php echo Text::_('COM_TJVENDOR_VENDOR_PROFILE');?>
</h1>
<input type="hidden" name="client" value="<?php echo $this->client; ?>" />
<div class="profile row <?php echo COM_TJVENDORS_WRAPPAER_CLASS;?>" id="tjv-wrapper">
	<div class="col-sm-12">
		<h3 class="mt-0">
			<?php echo $this->escape($this->vendor->getTitle());?>
			<span class="pull-right">
			<small>
			<a  href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $this->client . '&vendor_id=' . $this->vendor->vendor_id );?>">
			<i class="fa fa-wrench" aria-hidden="true"></i>  <?php echo Text::_("COM_TJVENDORS_VENDOR_UPDATE"); ?></a>
			</small>
			</span>
		</h3>
	</div>
	<div class="controls col-sm-3 center">
		<img  src="<?php echo $this->vendor->getLogo(); ?>" width="100%">
	</div>
	<div class="col-sm-9">
		<div>
			<div class='profile__content text-muted'>
				<?php echo $this->escape($this->vendor->getDescription());?>
			</div>
		</div>
	</div>
</div>
<script>
	tjVAdmin.vendor.readMore();
</script>

