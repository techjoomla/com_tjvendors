<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright  2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/tjvendors.php');

/**
 * Vendor controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendor extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'vendors';

		parent::__construct();
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'vendor_id')
	{
		$input  = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');
		$vendor_id = $input->get('vendor_id', '', 'STRING');
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&client=' . $client . '&vendor_id=' . $vendor_id;

		return $append;
	}

	/**
	 * Check for duplicate users
	 *
	 * @return null
	 *
	 * @since   1.6
	 */
	public function checkDuplicateUser()
	{
		$input  = JFactory::getApplication()->input->post;
		$user = $input->get('user', '', 'STRING');
		$model = $this->getModel('vendor');
		$results = $model->checkDuplicateUser($user);

		echo json_encode($results);
		jexit();
	}

	/**
	 * Vendor approval
	 *
	 * @return null
	 *
	 * @since   1.1
	 */
	public function vendorApprove()
	{
		$input  = JFactory::getApplication()->input;
		$vendor_id = $input->post->get('vendor_id', '', 'INTEGER');
		$vendorApprove = $input->post->get('vendorApprove', '', 'INTEGER');
		$client = $input->get('client', '', 'STRING');
		$model = $this->getModel('vendor');
		$data['vendor_id'] = $vendor_id;
		$data['vendor_client'] = $client;
		$data['approved'] = $vendorApprove;
		$result = $model->save($data);

		echo new JResponseJson($result, JText::_('COM_TJVENDORS_VENDOR_APPROVAL_ERROR'), true);
	}

	/**
	 * Build payment gateway fields
	 *
	 * @return null
	 *
	 * @since   1.6
	 */
	public function generateGatewayFields()
	{
		$input  = JFactory::getApplication()->input->post;
		$payment_gateway = $input->get('payment_gateway', '', 'STRING');
		$vendor_id = $input->get('vendor_id', '', 'INTEGER');
		$model = $this->getModel('vendor');
		$results = $model->generateGatewayFields($payment_gateway);
		echo json_encode($results);
		jexit();
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$input  = JFactory::getApplication()->input->post;
		$client = $input->get('client', '', 'STRING');
		$append = parent::getRedirectToListAppend();
		$append .= '&client=' . $client;

		return $append;
	}

	/**
	 * Save vendor data
	 *
	 * @param   integer  $key     key.
	 *
	 * @param   integer  $urlVar  url
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('Vendor', 'TjvendorsModel');
		$input = $app->input;
		$client = $input->get('client', '', 'STRING');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		$all_jform_data = $data;
		$data['paymentForm'] = $app->input->get('jform', array(), 'ARRAY');

		// Validate the posted data.
		$data = $model->validate($form, $data);
		$data['paymentForm'] = $app->input->get('jform', array(), 'ARRAY');

		if (!empty($data['paymentForm']))
		{
			foreach ($data['paymentForm']['payment_fields'] as $key => $field)
			{
				$paymentDetails[$key] = $field;
			}

			foreach ($data['paymentForm'] as $key => $detail)
			{
				$paymentPrefix = 'payment_';

				if (strpos($key, $paymentPrefix) !== false)
				{
					if ($key != 'payment_fields')
					{
						$paymentDetails[$key] = $detail;
					}
				}
			}
		}

		if (!empty($paymentDetails))
		{
			$data['paymentDetails'] = json_encode($paymentDetails);
			$data['gateway'] = $paymentDetails['payment_gateway'];
		}

		// On a clientless vendor registration
		if (empty($data['vendor_client']))
		{
			$data['params'] = $data['paymentDetails'];
			$data['payment_gateway'] = $paymentForm['payment_gateway'];
		}
		else
		{
			$data['payment_gateway'] = '';
			$data['params'] = '';
		}

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tjvendors.edit.vendor.id');
			$app->setUserState('com_tjvendors.edit.vendor.data', $all_jform_data);

			$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $client . '&vendor_id=' . $id, false));

			return false;
		}

		$paymentData = array_diff_key($all_jform_data, $data);
		$data['paymentForm'] = $paymentData;
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			$app->setUserState('com_tjvendors.edit.vendor.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tjvendors.edit.vendor.id');
			$this->setMessage(JText::sprintf('COM_TJVENDORS_VENDOR_ERROR_MSG_SAVE', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $client . '&vendor_id=' . $id, false));

			return false;
		}

		$msg      = JText::_('COM_TJVENDORS_MSG_SUCCESS_SAVE_VENDOR');
		$id = $input->get('id');

		if (empty($id))
		{
			$id = $return;
		}

		$task = $input->get('task');

		if ($task == 'apply')
		{
			$redirect = JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=update&client=' . $client . '&vendor_id=' . $id, false);
			$app->redirect($redirect, $msg);
		}

		if ($task == 'save2new')
		{
			$redirect = JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $client, false);
			$app->redirect($redirect, $msg);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_tjvendors.edit.vendor.id', null);

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Redirect to the list screen.
		$redirect = JRoute::_('index.php?option=com_tjvendors&view=vendors&client=' . $client, false);
		$app->redirect($redirect, $msg);

		// Flush the data from the session.
		$app->setUserState('com_tjvendors.edit.vendor.data', null);
	}
}
