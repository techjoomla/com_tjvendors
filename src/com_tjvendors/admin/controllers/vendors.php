<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright  2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Vendors list controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendors extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'vendor', $prefix = 'TjvendorsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method for delete vendor
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		$input  = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');
		parent::delete();
		$redirect = 'index.php?option=com_tjvendors&view=vendors&client=' . $client;
		$this->setRedirect($redirect);
	}

	/**
	 * Method for publish vendor
	 *
	 * @return  boolean
	 */
	public function publish()
	{
		$input  = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');
		parent::publish();
		$redirect = 'index.php?option=com_tjvendors&view=vendors&client=' . $client;
		$this->setRedirect($redirect);
	}
}
