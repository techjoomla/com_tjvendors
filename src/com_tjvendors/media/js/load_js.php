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
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// Add Javascript vars in array
$doc->addScriptOptions('com_tjvendor', array());

// Load JS files
JHtml::script(JUri::root() . 'media/com_tjvendor/js/core/class.js');
JHtml::script(JUri::root() . 'media/com_tjvendor/js/com_tjvendor.js');
JHtml::script(JUri::root() . 'media/com_tjvendor/js/core/base.js');
JHtml::script(Juri::root() . 'media/com_tjvendor/js/services/common.js');
JHtml::script(Juri::root() . 'media/com_tjvendor/js/ui/common.js');
