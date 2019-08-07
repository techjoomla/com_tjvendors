<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Tjvendors', JPATH_COMPONENT);

JLoader::register('TjvendorsController', JPATH_COMPONENT . '/controller.php');
$TjvendorFrontHelper = JPATH_ROOT . '/components/com_tjvendors/helpers/fronthelper.php';

if (!class_exists('TjvendorFrontHelper'))
{
	JLoader::register('TjvendorFrontHelper', $TjvendorFrontHelper);
	JLoader::load('TjvendorFrontHelper');
}

// Execute the task.
$controller = JControllerLegacy::getInstance('Tjvendors');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
$document = JFactory::getDocument();
$options['relative'] = true;
JHtml::_('script', 'com_tjvendor/tjvendors.js', $options);

// Frontend css
JHtml::_('stylesheet', 'com_tjvendor/tjvendors.css', $options);
JHtml::stylesheet(JUri::root() . 'media/techjoomla_strapper/vendors/no-more-tables.css', array(), true);

// Load Boostrap
$params        = ComponentHelper::getParams('com_tjvendors');
$loadBootstrap = $params->get('load_bootstrap');

if ($loadBootstrap == '1')
{
	define('COM_TJVENDORS_WRAPPAER_CLASS', "tjBs3");
	JHtml::_('stylesheet', 'media/techjoomla_strapper/bs3/css/bootstrap.min.css');
	JHtml::_('stylesheet', 'media/techjoomla_strapper/vendors/font-awesome/css/font-awesome.min.css');
}
else
{
	define('COM_TJVENDORS_WRAPPAER_CLASS', '');
}
