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
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_tjvendors'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

include_once  JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Tjvendors', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Tjvendors');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
$document = JFactory::getDocument();
$document->addScript(JUri::root(true) . '/media/com_tjvendor/js/tjvendors.js');

$tjvendorFrontHelper = JPATH_ROOT . '/components/com_tjvendors/helpers/fronthelper.php';

if (!class_exists('TjvendorFrontHelper'))
{
	JLoader::register('TjvendorFrontHelper', $tjvendorFrontHelper);
	JLoader::load('TjvendorFrontHelper');
}

$tjvendorsHelper = JPATH_ADMINISTRATOR . '/components/com_tjvendors/helpers/tjvendors.php';

if (!class_exists('TjvendorsHelper'))
{
	JLoader::register('TjvendorsHelper', $tjvendorsHelper);
	JLoader::load('TjvendorsHelper');
}
