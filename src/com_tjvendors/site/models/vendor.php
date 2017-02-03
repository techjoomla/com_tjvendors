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
		$item = parent::getItem($pk);

		return $item;
	}

	/**
	 * Method for save vendor information
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

		$data['user_id'] = JFactory::getUser()->id;

		$currency = $data['currency'];

		if ($data['user_id'] != 0)
		{
			// To check if editing in registration form
			if ($data['vendor_id'])
			{
			$table->save($data);
			$vendor_id = (int) $this->getState($this->getName() . '.id');
			$app->setUserState('com_tjvendors.edit.vendor.vendor_id', $vendor_id);

			return true;
			}
			else
			{
			// Attempt to save data
			if ($table->save($data) === true)
			{
				$vendorId = $table->vendor_id;
				$client = $table->vendor_client;
				$currencies = json_decode($table->currency);

				// Attempt to save the data in fee table
				if (!empty($currencies))
				{
				if ($table->vendor_id)
				{
					foreach ($currencies as $currency)
					{
					$userdata = new stdClass;
					$userdata->vendor_id  = $vendorId;
					$userdata->client = $client;
					$userdata->currency = $currency;
					$userdata->percent_commission = '0';
					$userdata->flat_commission = '0';
					$result    = JFactory::getDbo()->insertObject('#__tjvendors_fee', $userdata);
					}

					$vendor_id = (int) $this->getState($this->getName() . '.id');
					$app->setUserState('com_tjvendors.edit.vendor.vendor_id', $vendor_id);

					return true;
				}
				}
			}
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
