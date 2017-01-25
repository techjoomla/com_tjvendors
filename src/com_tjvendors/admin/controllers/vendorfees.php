<?php
/**
 * @version    SVN: 
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
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
class TjvendorsControllerVendorFees extends JControllerAdmin
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
	public function getModel($name = 'vendorfee', $prefix = 'TjvendorsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method for back to previous page
	 *
	 * @return  boolean
	 */
	public function back()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$client = $input->get('client', '', 'STRING');

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendors&client=' . $client, false));
	}

	/**
	 * Method for delete vendor
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		$input  = JFactory::getApplication()->input;
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendor = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		$vendorDetail = $TjvendorsModelVendor->getItem();
		$vendorId = $input->get('vendor_id', '', 'INT');
		$model      = $this->getModel('vendorfees');
		$post       = JRequest::get('post');

		$tj_vendors_id = $post['cid'];

		$result = $model->deleteVendorfee($tj_vendors_id);

		if ($result)
		{
			$redirect = 'index.php?option=com_tjvendors&view=vendorfees&vendor_id=' . $vendorId;
			$msg = JText::_('COM_TJVENDORS_RECORD_DELETED');
		}
		else
		{
			$redirect = 'index.php?option=com_tjvendors&view=vendorfees&vendor_id=' . $vendorId;
			$msg = JText::_('COM_TJVENDORS_ERR_DELETED');
		}

		$this->setRedirect($redirect, $msg);
	}
}
