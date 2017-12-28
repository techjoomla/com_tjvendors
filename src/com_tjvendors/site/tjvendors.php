<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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
$document->addStyleSheet(JPATH_ROOT . 'media/techjoomla_strapper/vendors/no-more-tables/no-more-tables.css');
