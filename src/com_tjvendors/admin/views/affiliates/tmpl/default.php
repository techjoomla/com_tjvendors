<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
 
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user      = Factory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

?>
<form action="<?php echo Route::_('index.php?option=com_tjvendors&view=affiliates'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
	<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>
		<?php
		if(empty($this->items))
		{?>
			<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo Text::_('COM_TJVENDOR_NO_MATCHING_RESULTS'); ?>
				</div>
		<?php
		}
		else
		{?>

		<table class="table table-striped" id="affiliatesList">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>

					<?php if (isset($this->items[0]->state)) :?>
					<th width="1%" >
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
					<?php endif?>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_NAME', 'a.`name`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_CODE', 'a.`code`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_DESCRIPTION', 'a.`description`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_COMMISSION', 'a.`commission`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_FORM_LBL_USER_COMMISSION', 'a.`user_commission`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_LIMIT', 'a.`affiliates_limit`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_VALID_FROM', 'a.`valid_from`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_VALID_TO', 'a.`valid_to`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_ID', 'a.`id`', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJVENDORS_AFFILIATE_VENDOR_ID', 'a.`vendor_id`', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				foreach ($this->items as $i => $item)
				{
					$canEdit   = $this->canDo->get('core.edit');
					$canChange	= $user->authorise('core.edit.state',	'com_tjvendors');
					?>
					<tr class="row<?php echo $i % 2; ?>">

							<td class="hidden-phone">
								<?php if ($canEdit || $canChange) : ?>
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								<?php endif; ?>
							</td>
							
							<td>
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'affiliates.', $canChange, 'cb'); ?>
							</td>
							<td>
							<?php if ($canEdit) : ?>
								<a href="<?php echo Route::_('index.php?option=com_tjvendors&task=affiliate.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
							</td>
							
							<td>
								<?php echo $this->escape($item->code); ?>
							</td>
							
							<td>
								<?php echo $this->escape($item->description); ?>
							</td>
							
							<td>
								<?php echo $this->escape($item->commission); ?>
							</td>
							<td>
								<?php echo $this->escape($item->user_commission); ?>
							</td>
							<td>
								<?php echo $item->affiliates_limit; ?>
							</td>
							
							<td>
								<?php echo HTMLHelper::_('date', $item->valid_from, Text::_('DATE_FORMAT_LC6')); ?>
							</td>
							
							<td>
								<?php echo HTMLHelper::_('date', $item->valid_to, Text::_('DATE_FORMAT_LC6')); ?>
							</td>
							<td>
								<?php echo $this->escape($item->id); ?>
							</td>

							<td>
								<?php echo $this->escape($item->vendor_id); ?>
							</td>
						</tr>
				<?php
				}?>
			</tbody>
		</table>
		<?php
		}?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
