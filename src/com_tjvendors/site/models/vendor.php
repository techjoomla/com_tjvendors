<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

/**
 * Tjvendors model.
 *
 * @since  1.6
 */
class TjvendorsModelVendor extends JModelAdmin
{
	/**
	 * @var    string  client data
	 * @since  1.6
	 */
	private $vendor_client = '';

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
		$form = $this->loadForm('com_tjvendors.vendor', 'vendor', array(
			'control' => 'jform', 'load_data' => $loadData)
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

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Create a new query object.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			$app = JFactory::getApplication();
			$input = $app->input;
			$client = $input->get('client', '', 'STRING');

			$user = JFactory::getUser()->id;

			// Select the required field from the table.
			$query->select('*')
				->from($db->quoteName('#__tjvendors_vendors'))
				->where('user_id=' . $db->quote($user))
				->where('vendor_client=' . $db->quote($client));

			$db->setQuery($query);
			$item = $db->loadObject();
		}

		return $item;
	}

	/**
	 * Method to duplicate an Vendor
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (! $user->authorise('core.create', 'com_tjvendors'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->vendor_id = 0;

				if (! $table->check())
				{
					throw new Exception($table->getError());
				}

				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || ! $table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
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
		$table = $this->getTable();
		$db = JFactory::getDBO();

		$app = JFactory::getApplication();
		$input = $app->input;

		$client = $input->get('client', '', 'STRING');
		$currencies = $input->get('currency', '', 'ARRAY');

		// Convert currency into json
		$data['currency'] = json_encode($currencies);

		$data['user_id'] = JFactory::getUser()->id;
		$data['vendor_client'] = ! empty($client) ? $client : $data['vendor_client'];

		// Bind data
		if (! $table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Validate
		if (! $table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		if ($data['vendor_id'] == 0)
		{
			if (! $table->checkDuplicateUser())
			{
				$app->enqueueMessage(JText::_('COM_TJVENDORS_EXIST_RECORDS'), 'warning');

				return false;
			}
		}

		if ($data['user_id'] != 0)
		{
			// Attempt to save data
			if (parent::save($data))
			{
				return true;
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_TJVENDORS_SELECT_USER'), 'warning');

			return false;
		}

		return false;
	}

	/**
	 * Set the onject values
	 *
	 * @param   string  $client  client value
	 *
	 * @return mixed
	 */
	public function setClient($client)
	{
		$this->vendor_client = $client;
	}

	/**
	 * Get an client value
	 *
	 * @return mixed
	 */
	public function getClient()
	{
		return $this->vendor_client;
	}

	/**
	 * This function will return the vendor details based on current user and client.
	 *
	 * @param   INT     $user    User id
	 * @param   STRING  $client  Component client
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	public function getVendorDetails($user = "", $client = "")
	{
		$result = array();
		$user = empty($user) ? JFactory::getUser() : JFactory::getUser($user);

		if ($user->id)
		{
			// Load vendor details based on client
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select("*");
			$query->from($db->qn("#__tjvendors_vendors"));
			$query->where($db->qn("user_id") . " = " . $user->id);

			if (! empty($client))
			{
				$query->where($db->qn("vendor_client") . " = " . $db->q($client));
			}

			$db->setQuery($query);
			$result = $db->loadAssoc();

			// Load default entry if available
			if (empty($result) && !empty($client))
			{
				$query = $db->getQuery(true);
				$query->select("*");
				$query->from($db->qn("#__tjvendors_vendors"));
				$query->where($db->qn("user_id") . " = " . $user->id);
				$query->where($db->qn("vendor_client") . "  = ''");

				$db->setQuery($query);
				$result = $db->loadAssoc();
			}
		}

		return $result;
	}
}
