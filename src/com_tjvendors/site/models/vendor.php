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
require_once JPATH_ADMINISTRATOR . '/components/com_tjvendors/helpers/tjvendors.php';
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
		$app = JFactory::getApplication();
		$input = $app->input;
		$client = $input->get('client', '', 'STRING');

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_tjvendors.edit.vendor.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			if (!empty($this->item->vendor_id))
			{
				if (!empty($client))
				{
					$tjvendorsHelpersTjvendors = new TjvendorsHelpersTjvendors;
					$gatewayDetails = $tjvendorsHelpersTjvendors->getPaymentDetails($this->item->vendor_id, $client);

					if (!empty($gatewayDetails))
					{
						$this->item->payment_gateway = $gatewayDetails->payment_gateway;
					}
				}
			}

			$data = $this->item;
			$this->item->vendor_client = "";
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
	 * @param   Integer  $vendorId         used for inserting data for that vendor
	 *
	 * @param   Integer  $payment_gateway  used for inserting data for that vendor
	 *
	 * @param   Integer  $paymentDetails   payment details
	 *
	 * @return id
	 */
	public function addMultiVendor($vendorId, $payment_gateway, $paymentDetails)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('max(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName('#__vendor_client_xref'));
		$db->setQuery($query);
		$res = $db->loadResult();
		$fields = array($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id),
		$db->quoteName('payment_gateway') . ' = ' . $db->quote($payment_gateway),
		$db->quoteName('params') . ' = ' . $db->quote($paymentDetails),
		);

		// Conditions for which records should be updated.
		$conditions = array($db->quoteName('id') . ' = ' . $res);

		$query->update($db->quoteName('#__vendor_client_xref'))->set($fields)->where($conditions);
		$db->setQuery($query);

		$result = $db->execute();

		return true;
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
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$table = $this->getTable();
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$paymentForm = $app->input->get('jform', array(), 'ARRAY');

		foreach ($paymentForm as $key => $detail)
		{
			$paymentPrefix = 'payment_';

			if (strpos($key, $paymentPrefix) !== false)
			{
				$paymentGateway[$key] = $detail;
			}
		}

		$paymentDetails["payment_gateway"] = $paymentGateway;

		$encodedPaymentDetails = json_encode($paymentDetails);

		// $data['primaryEmail'] = 0;

		if (empty($data['vendor_client']))
		{
			$data['params'] = $encodedPaymentDetails;
		}

		$data['user_id'] = JFactory::getUser()->id;

		if ($data['user_id'] != 0)
		{
			// To check if editing in registration form
			if ($data['vendor_id'])
			{
				$table->save($data);

				// $app->setUserState('com_tjvendors.edit.vendor.vendor_id', $vendor_id);

				if (!empty($data['vendor_client']))
				{
					$checkForDuplicateClient = TjvendorsHelpersTjvendors::checkForDuplicateClient($data['vendor_id'], $data['vendor_client']);

					if ($checkForDuplicateClient != $data['vendor_client'])
					{
						$client_entry = new stdClass;
						$client_entry->client = $data['vendor_client'];
						$client_entry->payment_gateway = $paymentForm['payment_gateway'];
						$client_entry->vendor_id = $data['vendor_id'];
						$client_entry->params = $encodedPaymentDetails;

						// Insert the object into the user profile table.
						$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
					}
					else
					{
							$app->enqueueMessage(JText::_('COM_TJVENDORS_DUPLICATE_CLIENT_ERROR'), 'warning');

						return false;
					}

					return true;
				}

				return true;
			}
			else
			{
			// Attempt to save data
				if ($table->save($data) === true)
				{
					$vendorId = $table->vendor_id;

					if (!empty($data['vendor_client']))
					{
						$payment_gateway = $paymentForm['payment_gateway'];
						$client_entry = new stdClass;
						$client_entry->client = $data['vendor_client'];
						$client_entry->vendor_id = $data['vendor_id'];
						$client_entry->payment_gateway = $payment_gateway;
						$client_entry->params = $encodedPaymentDetails;

						// Insert the object into the user profile table.
						$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
						$this->addMultiVendor($vendorId, $payment_gateway, $encodedPaymentDetails);
					}

					return true;
				}

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

	/**
	 * Method to build Form.
	 *
	 * @param   string  $payment_gateway  payment gateway.
	 *
	 * @return   array result
	 *
	 * @since    1.6
	 */
	public function generateGatewayFields($payment_gateway)
	{
		$form_path = JPATH_SITE . '/plugins/payment/' . $payment_gateway . '/' . $payment_gateway . '/form/' . $payment_gateway . '.xml';
		$test = $payment_gateway . '_' . 'plugin';
		$form = JForm::getInstance($test, $form_path);
		$fieldSet = $form->getFieldset('payment_gateway');
		$html = array();

		foreach ($fieldSet as $field)
		{
			$html[] = $field->renderField();
		}

		return $html;
	}
}
