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
	 * Method to clone existing Vendors
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_TJVENDORS_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_TJVENDORS_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_tjvendors&view=vendors');
	}

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
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
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
		$currUrl = TjvendorsHelpersTjvendors::getCurrency();

		$model      = $this->getModel('vendors');
		$post       = JRequest::get('post');

		$tj_vendors_id = $post['cid'];

		$result = $model->deleteVendor($tj_vendors_id);

		if ($result)
		{
			$redirect = 'index.php?option=com_tjvendors&view=vendors&client=' . $client . $currUrl;
			$msg = JText::_('COM_TJVENDORS_RECORD_DELETED');
		}
		else
		{
			$redirect = 'index.php?option=com_tjvendors&view=vendors&client=' . $client . $currUrl;
			$msg = JText::_('COM_TJVENDORS_ERR_DELETED');
		}

		$this->setRedirect($redirect, $msg);
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
		$currUrl = TjvendorsHelpersTjvendors::getCurrency();
		parent::publish();
		$redirect = 'index.php?option=com_tjvendors&view=vendors&client=' . $client . $currUrl;
		$this->setRedirect($redirect);
	}
}
