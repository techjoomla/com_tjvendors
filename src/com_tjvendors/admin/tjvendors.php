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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_tjvendors'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Tjvendors', JPATH_COMPONENT_ADMINISTRATOR);

$controller = BaseController::getInstance('Tjvendors');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
$document = Factory::getDocument();
$document->addScript(Uri::root(true) . '/media/com_tjvendor/js/tjvendors.js');

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
