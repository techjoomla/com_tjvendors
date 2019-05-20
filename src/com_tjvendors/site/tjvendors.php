<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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
$params         = ComponentHelper::getParams('com_tjvendors');
$load_bootstrap = $params->get('load_bootstrap');

if ($load_bootstrap == '1')
{
	define('COM_TJVENDORS_BS_CLASS_CONST', "tjBs3");
	JHtml::_('stylesheet', 'media/techjoomla_strapper/bs3/css/bootstrap.min.css');
	JHtml::_('stylesheet', 'media/techjoomla_strapper/vendors/font-awesome/css/font-awesome.min.css');
}
else
{
	define('COM_TJVENDORS_BS_CLASS_CONST', '');
}
