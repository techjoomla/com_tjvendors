<?php
/**
 * @package     TJVendors
 * @subpackage  Layout
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/*
 * As Joomla doesn't provide bootstrap 3 layout and TJVendor run on bootstrap 3 templates added the overrides for the Joomla subform layout
*/

/**
 * Make thing clear
 *
 * @var Form   $tmpl             The Empty form for template
 * @var array   $forms            Array of Form instances for render the rows
 * @var bool    $multiple         The multiple state for the form field
 * @var int     $min              Count of minimum repeating in multiple mode
 * @var int     $max              Count of maximum repeating in multiple mode
 * @var string  $fieldname        The field name
 * @var string  $control          The forms control
 * @var string  $label            The field label
 * @var string  $description      The field description
 * @var array   $buttons          Array of the buttons that will be rendered
 * @var bool    $groupByFieldset  Whether group the subform fields by it`s fieldset
 */

extract($displayData);

// Add script
if ($multiple)
{
	HTMLHelper::_('jquery.ui', array('core', 'sortable'));
	HTMLHelper::_('script', 'system/subform-repeatable.js', array('version' => 'auto', 'relative' => true));
}

$sublayout = empty($groupByFieldset) ? 'section' : 'section-byfieldsets';
?>

<div class="row-fluid">
	<div class="subform-repeatable-wrapper subform-layout">
		<div class="subform-repeatable"
			data-bt-add="a.group-add-<?php echo $unique_subform_id; ?>"
			data-bt-remove="a.group-remove-<?php echo $unique_subform_id; ?>"
			data-bt-move="a.group-move-<?php echo $unique_subform_id; ?>"
			data-repeatable-element="div.subform-repeatable-group-<?php echo $unique_subform_id; ?>"
			data-minimum="<?php echo $min; ?>" data-maximum="<?php echo $max; ?>"
		>

			<?php if (!empty($buttons['add'])) : ?>
			<div class="btn-toolbar hidden">
				<div class="btn-group">
					<a class="btn btn-mini button btn-success group-add-<?php echo $unique_subform_id; ?>"
					aria-label="<?php echo Text::_('JGLOBAL_FIELD_ADD'); ?>">
						<span class="icon-plus"></span>
					</a>
				</div>
			</div>
			<?php endif; ?>
		<?php
		foreach ($forms as $k => $form) :
			echo $this->sublayout(
				$sublayout,
				array(
					'form' => $form,
					'basegroup' => $fieldname,
					'group' => $fieldname . $k,
					'buttons' => $buttons,
					'unique_subform_id' => $unique_subform_id,
				)
			);
		endforeach;
		?>
		<?php if ($multiple) : ?>
			<template class="subform-repeatable-template-section"><?php echo trim(
				$this->sublayout(
					$sublayout,
					array(
						'form' => $tmpl,
						'basegroup' => $fieldname,
						'group' => $fieldname . 'X',
						'buttons' => $buttons,
						'unique_subform_id' => $unique_subform_id,
					)
				)
			); ?></template>
		<?php endif; ?>
		</div>
	</div>
</div>
