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

$form = $forms[0];
?>

<div class="subform-wrapper">
<?php foreach ($form->getGroup('') as $field) : ?>
	<?php echo $field->renderField(); ?>
<?php endforeach; ?>
</div>

