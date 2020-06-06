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

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');
?>

<form action="<?php echo Route::_('index.php?option=com_tjvendors&view=affiliate&id=' .$this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-horizontal">
		<fieldset class="adminform">
			<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

			<?php echo $this->form->renderField('id'); ?>
			<?php echo $this->form->renderField('vendor_id'); ?>
			<?php echo $this->form->renderField('name'); ?>
			<?php echo $this->form->renderField('code'); ?>
			<?php echo $this->form->renderField('description'); ?>
			<?php echo $this->form->renderField('commission_type'); ?>
			<?php echo $this->form->renderField('commission'); ?>
			<?php echo $this->form->renderField('user_commission'); ?>
			<?php echo $this->form->renderField('affiliates_limit'); ?>
			<?php echo $this->form->renderField('max_per_user'); ?>
			<?php echo $this->form->renderField('valid_from'); ?>
			<?php echo $this->form->renderField('valid_to'); ?>
			<?php echo $this->form->renderField('state'); ?>
			<?php echo $this->form->renderField('params'); ?>
		</fieldset>
	</div>
	<input type="hidden" name="task" value="affiliate.save"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
