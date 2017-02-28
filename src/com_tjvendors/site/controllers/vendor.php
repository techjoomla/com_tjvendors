<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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

		if (empty($this->vendor_client))
		{
			$this->client = JFactory::getApplication()->input->get('jform', array(), 'array')['vendor_client'];
		}

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

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Vendor', 'TjvendorsModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (! $form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
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
			$app->setUserState('com_tjvendors.edit.vendor.data', $app->input->get('jform', array(), "ARRAY"));

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tjvendors.edit.vendor.vendor_id');
			$client = $app->getUserState('com_tjvendors.edit.vendor.client');

			$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=edit&vendor_id=' . $id . '&client=' . $client, false));

			return false;
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			$input = JFactory::getApplication();
			$vendor_id = $input->get('vendor_id', '', 'INTEGER');

			// Save the data in the session.
			$app->setUserState('com_tjvendors.edit.vendor.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tjvendors.edit.vendor.id');
			$client = $app->getUserState('com_tjvendors.edit.vendor.client');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$dynamicLink = '&client=' . $data['vendor_client'] . '&vendor_id=' . $data['vendor_id'];
			$this->setRedirect(
					JRoute::_(
					'index.php?option=com_tjvendors&view=vendor&status=register&layout=edit' . $dynamicLink, false
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

		// Redirect to the list screen.
		$this->setMessage(JText::_('COM_TJVENDORS_ITEM_SAVED_SUCCESSFULLY'));
		$this->setRedirect(
				JRoute::_(
				'index.php?option=com_tjvendors&view=vendor&layout=default&vendor_id=' . $vendor_id . '&client=' . $data['vendor_client'], false
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
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$this->setRedirect(
		JRoute::_('index.php?option=com_tjvendors&view=vendor&vendor_id=' . $data['vendor_id'] . '&client=' . $data['vendor_client'], false)
		);
	}
}
