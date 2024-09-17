<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Vendors list controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerAffliates extends JControllerAdmin
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
	public function getModel($name = 'Affliates', $prefix = 'TjvendorsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method for delete affliates
	 *
	 * @return  boolean
	 */
/*
	public function delete()
	{
		/* // Check for request forgeries
		Session::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$input  = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$model = $this->getModel("affliates");

		foreach ($cid as $affliate_id)
		{
			$model->deleteClientFromVendor($affliate_id, $client);
		}

		$redirect = 'index.php?option=com_tjvendors&view=affliates&client=' . $client;
		$this->setRedirect($redirect);
	}*/
}
