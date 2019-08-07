<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

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
		$this->input = JFactory::getApplication()->input;

		$this->vendor_client = $this->input->get('client', '', 'STRING');

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
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&client=' . $this->vendor_client;

		return $append;
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
		$append = parent::getRedirectToListAppend();
		$append .= '&client=' . $this->vendor_client;

		return $append;
	}

	/**
	 * Method to save a vendor profile data.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$params = JComponentHelper::getParams('com_tjvendors');
		$vendorApproval = $params->get('vendor_approval');

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Vendor', 'TjvendorsModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$data['vendor_client'] = $app->input->get('client', '', 'STRING');

		$data['user_id'] = JFactory::getUser()->id;

		// Validate the posted data.
		$form = $model->getForm();

		if (! $form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		$validate  = $model->validate($form, $data);

		if ($vendorApproval && empty($data['vendor_id']))
		{
			$data['approved'] = 0;
			$data['state'] = 0;
		}
		else
		{
			$data['approved'] = 1;
			$data['state'] = 1;
		}

		// Check for errors
		if ($validate === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n; $i ++)
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
			// Save the data in the session.
			$app->setUserState('com_tjvendors.edit.vendor.data', $data);

			// Redirect back to the edit screen.
			$id = $app->input->get('vendor_id', '', 'INTEGER');
			$client = $app->input->get('client', '', 'STRING');

			if ($id != 0)
			{
				$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=profile&vendor_id=' . $id . '&client=' . $client, false));
			}
			else
			{
				$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&vendor_id=' . $id . '&client=' . $client, false));
			}

			return false;
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_tjvendors.edit.vendor.data', $all_jform_data);

			// Redirect back to the edit screen.
			$client = $app->input->get('client', '', 'STRING');

			$id = $app->getUserState('com_tjvendors.edit.vendor.data.vendor_id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$dynamicLink = '&client=' . $data['vendor_client'] . '&vendor_id=' . $id;

			$layout = $id != 0 ? 'profile' : 'edit';

			$this->setRedirect(
					JRoute::_(
					'index.php?option=com_tjvendors&view=vendor&layout=' . $layout . $dynamicLink, false
					)
					);

			return false;
		}

		$user_id = Jfactory::getUser()->id;

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('vendor_id'));
		$query->from($db->quoteName('#__tjvendors_vendors'));
		$query->where($db->quoteName('user_id') . ' = ' . $user_id);
		$db->setQuery($query);
		$vendor_id = $db->loadResult();
		$input = JFactory::getApplication()->input;

		// Redirect to the list screen.
		$this->setMessage(JText::_('COM_TJVENDORS_MSG_SUCCESS_SAVE_VENDOR'));
		$this->setRedirect(
				JRoute::_(
				'index.php?option=com_tjvendors&view=vendor&layout=default&vendor_id=' . $vendor_id . '&client=' . $input->get('client', '', 'STRING'), false
				)
				);

		// Flush the data from the session.
		$app->setUserState('com_tjvendors.edit.vendor.data', null);
	}

	/**
	 * Cancel description
	 *
	 * @param   integer  $key  The key
	 *
	 * @return description
	 */
	public function cancel($key=null)
	{
		$input = JFactory::getApplication()->input;
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$this->setRedirect(
		JRoute::_('index.php?option=com_tjvendors&view=vendor&vendor_id=' . $data['vendor_id'] . '&client=' . $input->get('client', '', 'STRING'), false)
		);
	}

	/**
	 * Build a form
	 *
	 * @return null
	 *
	 * @since   1.6
	 */
	public function generateGatewayFields()
	{
		$input  = JFactory::getApplication()->input->post;
		$payment_gateway = $input->get('payment_gateway', '', 'STRING');
		$parentTag = $input->get('parent_tag', '', 'STRING');
		$vendor_id = $input->get('vendor_id', '', 'INTEGER');
		$model = $this->getModel('vendor');
		$results = $model->generateGatewayFields($payment_gateway, $parentTag);
		echo json_encode($results);
		jexit();
	}
}
