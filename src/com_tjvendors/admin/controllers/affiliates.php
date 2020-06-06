<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Affiliate list controller class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsControllerAffiliates extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object|boolean	The Model
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	public function getModel($name = 'affiliate', $prefix = 'TjvendorsModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
}
