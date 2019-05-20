<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    SocialAds
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;

$params         = ComponentHelper::getParams('com_tjvendors');
$load_bootstrap = $params->get('load_bootstrap');

if ($load_bootstrap == '1')
{
	define('COM_TJVENDORS_BS_CLASS_CONST', 'tjBs3');
}
else
{
	define('COM_TJVENDORS_BS_CLASS_CONST', '');
}
