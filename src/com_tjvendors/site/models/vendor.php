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
JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
JLoader::import('tjvendors', JPATH_ADMINISTRATOR . '/components/com_tjvendors/helpers');

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
		$app = JFactory::getApplication();
		$input = $app->input;
		$client = $input->get('client', '', 'STRING');

		$data = JFactory::getApplication()->getUserState('com_tjvendors.edit.vendor.data', array());

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
					$tjvendorFrontHelper = new TjvendorFrontHelper;
					$gatewayDetails = $tjvendorFrontHelper->getPaymentDetails($this->item->vendor_id, $client);

					if (!empty($gatewayDetails))
					{
						$this->item->payment_gateway = $gatewayDetails->payment_gateway;
					}
				}
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
	 * @param   Integer  $vendor_id        used for inserting data for that vendor
	 *
	 * @param   Integer  $payment_gateway  used for inserting data for that vendor
	 *
	 * @param   Integer  $paymentDetails   payment details
	 *
	 * @return id
	 */
	public function addMultiVendor($vendor_id,$payment_gateway, $paymentDetails)
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
	 * Method to check duplicate user.
	 *
	 * @param   integer  $user_id  user name.
	 *
	 * @return   array rows
	 *
	 * @since    1.6
	 */
	public function checkDuplicateUser($user_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
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
	 * Method to generate payment gateway fields.
	 *
	 * @param   string  $payment_gateway  payment gateway.
	 *
	 * @return   array result
	 *
	 * @since    1.6
	 */
	public function generateGatewayFields($payment_gateway)
	{
		$app = JFactory::getApplication();
		$client = $app->getUserStateFromRequest('vendor.client', 'vendor.client');
		$vendor_id = $app->getUserStateFromRequest('vendor.vendor_id', 'vendor.vendor_id');
		$tjvendorFrontHelper = new TjvendorFrontHelper;
		$vendorDetails = $tjvendorFrontHelper->getPaymentDetails($vendor_id, $client);

		if (!empty($vendorDetails))
		{
			$paymentDetailsArray = json_decode($vendorDetails->params);
		}

		$form_path = JPATH_SITE . '/plugins/payment/' . $payment_gateway . '/' . $payment_gateway . '/form/' . $payment_gateway . '.xml';
		$test = $payment_gateway . '_' . 'plugin';

		if (jFile::exists($form_path))
		{
			$form = JForm::getInstance($test, $form_path, array('control' => 'jform[payment_fields]'));

			if (!empty($vendor_id))
			{
				$paymentDetails = array();

				if (!empty($paymentDetailsArray))
				{
					foreach ($paymentDetailsArray as $key => $detail)
					{
						if ($key != "payment_gateway")
						{
							$paymentDetails[$key] = $detail;
						}
					}
				}

				$form->bind($paymentDetails);
			}

			$fieldSet = $form->getFieldset('payment_gateway');
			$html = array();

			foreach ($fieldSet as $field)
			{
				$html[] = $field->renderField();
			}

			return $html;
		}

		return false;
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
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', '', 'STRING');
		$app = JFactory::getApplication();
		$site = $app->isSite();

		JLoader::import('components.com_tjvendors.events.vendor', JPATH_SITE);
		$tjvendorTriggerVendor = new TjvendorTriggerVendor;

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
		}

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

			// To check if editing in registration form
			if ($data['vendor_id'])
			{
				$table->save($data);
				$tjvendorFrontHelper = new TjvendorFrontHelper;
				$vendorClients = $tjvendorFrontHelper->getClientsForVendor($data['vendor_id']);
				$count = 0;

				foreach ($vendorClients as $client)
				{
					if ($client == $data['vendor_client'])
					{
						$count++;
					}
				}

				if ($count == 0)
				{
					$client_entry = new stdClass;
					$client_entry->client = $data['vendor_client'];
					$client_entry->vendor_id = $data['vendor_id'];
					$client_entry->payment_gateway = $data['gateway'];
					$client_entry->params = $data['paymentDetails'];
					$client_entry->approved = $data['approved'];

					// Insert the object into the user profile table.
					JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
					$tjvendorTriggerVendor->onAfterVendorSave($data, true);

					return true;
				}
				else
				{
					$query = $db->getQuery(true);

					// Fields to update.
					if (isset($data['paymentDetails']))
					{
						$fields = array(
							$db->quoteName('params') . ' = ' . $db->quote($data['paymentDetails']),
							$db->quoteName('payment_gateway') . ' = ' . $db->quote($data['gateway']),
						);
					}
					else
					{
						$fields = array(
						$db->quoteName('approved') . ' = ' . $db->quote($data['approved']),
						);
					}

					// Conditions for which records should be updated.
						$conditions = array(
						$db->quoteName('vendor_id') . ' = ' . $db->quote($data['vendor_id']),
						$db->quoteName('client') . ' = ' . $db->quote($data['vendor_client'])
					);

					$query->update($db->quoteName('#__vendor_client_xref'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$result = $db->execute();

					/* Trigger on Vendor Edit / update*/
					$tjvendorTriggerVendor->onAfterVendorSave($data, false);

					return true;
				}
			}
			else
			{
				if ($table->save($data) === true)
				{
					$data['vendor_id'] = $table->vendor_id;

					if (!empty($data['vendor_client']))
					{
						$client_entry = new stdClass;
						$client_entry->client = $data['vendor_client'];
						$client_entry->vendor_id = $data['vendor_id'];
						$client_entry->payment_gateway = $data['gateway'];
						$client_entry->params = $data['paymentDetails'];
						$client_entry->approved = $data['approved'];

						// Insert the object into the vendor_client_xref table.
						$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
					}

					/* Send mail on vendor creation */
					$tjvendorTriggerVendor->onAfterVendorSave($data, true);

					return true;
				}

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
}
