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

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');
JHTML::_('behavior.modal', 'a.modal');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<form action="index.php?option=com_tjvendors&view=affliates" name="adminForm" id="adminForm" class="form-validate" method="post">

	<?php
		// Sorting and Searching Options
		// echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>
<div class="clearfix"> </div>
	<?php
	if (empty($this->items))
	{
	?>
	<div class="clearfix">&nbsp;</div>
	<div class="alert alert-no-items">
		<?php echo JText::_('NO_MATCHING_RESULTS'); ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php
	}
	else
	{
	?>
	<table class='table table-striped'>
		<thead>
			<tr>
				<th width="40%">
					<?php echo JText::_('ID')?>
				</th>
				<th width="50%">
					<?php echo JText::_('NAME');?>
				</th>
				<th width="50%">
					<?php echo JText::_('CODE');?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if (!empty($this->items)) : ?>
		<?php foreach ($this->items as $i => $row) :
			$link = JRoute::_('index.php?option=com_tjvendors&task=affliate.edit&id=' . $row->id);
		?>
		<tr>
			<td>
				<?php echo htmlspecialchars($row->id,  ENT_COMPAT, 'UTF-8'); ?>
			</td>
			<td>
				<?php echo htmlspecialchars($row->name,  ENT_COMPAT, 'UTF-8'); ?>
			</td>
			<td>
				<?php echo htmlspecialchars($row->code,  ENT_COMPAT, 'UTF-8'); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<?php
	}?>
<?php echo JHtml::_('form.token'); ?>
</form>
