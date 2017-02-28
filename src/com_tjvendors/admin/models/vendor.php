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

jimport('joomla.application.component.modeladmin');

/**
 * Tjvendors model.
 *
 * @since  1.6
 */
class TjvendorsModelVendor extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_TJVENDORS';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_tjvendors.vendor';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Vendor', $prefix = 'TjvendorsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_tjvendors.vendor', 'vendor',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tjvendors.edit.vendor.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$this->item->vendor_client = "";
			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to add vendor id after client is added to the table.
	 *
	 * @param   Array  $vendor_id  vendor id
	 * 
	 * @return   mixed
	 *
	 * @since    1.6
	 */
	public function addVendorId($vendor_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('max(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName('#__vendor_client_xref'));
		$db->setQuery($query);
		$res = $db->loadResult();
		$fields = array($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));

		// Conditions for which records should be updated.
		$conditions = array($db->quoteName('id') . ' = ' . $res);

		$query->update($db->quoteName('#__vendor_client_xref'))->set($fields)->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return true;
	}

	/**
	 * Method to check duplicate user.
	 *
	 * @param   integer  $user  user name.
	 * 
	 * @return   array rows
	 *
	 * @since    1.6
	 */
	public function checkDuplicateUser($user)
	{
		$user_id = $this->getUserId($user);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('*'));
		$query->from($db->quoteName('#__tjvendors_vendors'));

		if (!empty($user_id))
		{
			$query->where($db->quoteName('user_id') . ' = ' . $user_id);
		}

		$db->setQuery($query);
		$rows = $db->loadAssoc();

		if ($rows)
		{
			return $rows;
		}
	}

	/**
	 * Method to give user_id
	 *
	 * @param   integer  $user  user name.
	 * 
	 * @return   array rows
	 *
	 * @since    1.6
	 */
	public function getUserId($user)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote($user));
		$db->setQuery($query);
		$row = $db->loadResult();

		return $row;
	}

	/**
	 * Method for save user specific %commission, flat commission, client
	 *
	 * @param   Array  $data  Data
	 *
	 * @return id
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
		$table = $this->getTable();
		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', '', 'STRING');
		$logoDetails = $app->input->files->get('jform', array(), 'raw');
		$logoName = $logoDetails['vendor_logo']['name'];

		if ($data['user_id'] != 0)
		{
			// To check if editing in registration form
			if ($data['vendor_id'])
			{
				$table->save($data);

				if ($layout == "edit" && !empty($data['vendor_client']))
				{
					require_once JPATH_SITE . '/components/com_tjvendors/helpers/tjvendors.php';
					$tjvendorsHelpersTjvendors = new TjvendorsHelpersTjvendors;
					$checkForDuplicateClient = $tjvendorsHelpersTjvendors->checkForDuplicateClient($data['vendor_id'], $data['vendor_client']);

					if ($checkForDuplicateClient != $data['vendor_client'])
					{
						$vendor_id = (int) $this->getState($this->getName() . '.id');
						$client_entry = new stdClass;
						$client_entry->client = $data['vendor_client'];
						$client_entry->vendor_id = $data['vendor_id'];

						// Insert the object into the user profile table.
						$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
					}
					else
					{
						$app->enqueueMessage(JText::_('COM_TJVENDORS_DUPLICATE_CLIENT_ERROR'), 'warning');

						return false;
					}
				}

				return true;
			}
			else
			{
				if ($table->save($data) === true)
				{
					$vendorId = $table->vendor_id;

					if (!empty($data['vendor_client']))
					{
						$client_entry = new stdClass;
						$client_entry->client = $data['vendor_client'];
						$client_entry->vendor_id = $data['vendor_id'];

						// Insert the object into the user profile table.
						$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
						$this->addVendorId($vendorId);
					}

					return true;
				}

				return false;
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_TJVENDORS_SELECT_USER'), 'warning');

			return false;
		}

		return false;
	}
}
