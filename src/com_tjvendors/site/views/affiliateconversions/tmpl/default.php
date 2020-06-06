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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('bootstrap.tooltip');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<div class="affiliatelogs row-fluid">
	<div class="page-title">
		<h1><?php echo $this->document->getTitle(); ?></h1>
	</div>
	<form action="<?php echo Route::_('index.php?option=com_tjvendors&view=affiliateconversions');?>" method="post" id="adminForm" name="adminForm">

		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

		if (empty($this->items))
		{
			?>
			<div class="col-xs-12">
				<div class="alert alert-info">
					<?php echo Text::_('COM_TJVENDOR_NO_MATCHING_RESULTS'); ?>
				</div>
			</div>
			<?php
		}
		else
		{
		?>

		<div class="table-responsive">
			<table class="table table-striped tj-table" id="affiliateLogList">
				<thead>
					<tr>
						<th class=''>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJVENDORS_AFFLIATE_LIST_LBL_AFFILIATE_COVERSIONID', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th class=''>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJVENDORS_AFFLIATE_LIST_LBL_AFFILIATE_VENDOR', 'aff.vendor_id', $listDirn, $listOrder); ?>
						</th>
						<th class=''>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJVENDORS_AFFLIATE_LIST_LBL_AFFILIATE_CREATED_DATE', 'a.date', $listDirn, $listOrder); ?>
						</th>
						<th class=''>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJVENDORS_AFFLIATE_LIST_LBL_AFFILIATE_VALUE', 'a.value', $listDirn, $listOrder); ?>
						</th>
						<th class=''>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJVENDORS_AFFLIATE_LIST_LBL_USER_COMMISSION', 'a.commision', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$vendorsModel = TJVendors::model('Vendor');
						foreach ($this->items as $i => $item)
						{
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td><?php echo (int) $item->id; ?></td>
								<td><?php echo $this->escape($item->vendor); ?></td>
								<td><?php echo HTMLHelper::date($item->date); ?></td>
								<td><?php echo (int) $item->value; ?></td>
								<td><?php echo (int) $item->commision; ?></td>
							</tr>
							<?php
						}
					?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
					<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token');

		}?>
		</form>
</div>
