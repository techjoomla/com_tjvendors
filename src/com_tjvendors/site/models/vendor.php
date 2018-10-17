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
						$this->item->payment_gateway = json_decode($gatewayDetails->params)->payment_gateway;
					}
				}
			}

			$data = $this->item;
		}

		return $this->item;
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

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
		$vendorXref = JTable::getInstance('VendorClientXref', 'TjvendorsTable');
		$vendorXref->load(array('vendor_id' => $item->vendor_id));
		$item->params = $vendorXref->params;

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
	 * @return  boolean
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

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_TJVENDORS_DB_EXCEPTION_WARNING_MESSAGE'), 'error');
		}

		return true;
	}

	/**
	 * Method to check duplicate user.
	 *
	 * @param   integer  $user_id  user name.
	 *
	 * @return   array|boolean
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

		try
		{
			$rows = $db->loadAssoc();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_TJVENDORS_DB_EXCEPTION_WARNING_MESSAGE'), 'error');
		}

		if (empty($rows))
		{
			return false;
		}

		if ($rows)
		{
			return $rows;
		}
	}

	/**
	 * Method to generate payment gateway fields.
	 *
	 * @param   string  $payment_gateway  payment gateway.
	 * @param   string  $parentTag        To load payment form below the gateway list.
	 *
	 * @return   array result
	 *
	 * @since    1.6
	 */
	public function generateGatewayFields($payment_gateway, $parentTag)
	{
		$app = JFactory::getApplication();
		$client = $app->getUserStateFromRequest('vendor.client', 'vendor.client');
		$vendor_id = $app->getUserStateFromRequest('vendor.vendor_id', 'vendor.vendor_id');
		$tjvendorFrontHelper = new TjvendorFrontHelper;
		$vendorDetails = $tjvendorFrontHelper->getPaymentDetails($vendor_id, $client);

		if (!empty($vendorDetails))
		{
			$params = json_decode($vendorDetails->params)->payment_gateway;
		}

		$form_path = JPATH_SITE . '/plugins/payment/' . $payment_gateway . '/' . $payment_gateway . '/form/' . $payment_gateway . '.xml';
		$test = $payment_gateway . '_' . 'plugin';

		if (jFile::exists($form_path))
		{
			$form = JForm::getInstance($test, $form_path, array('control' => $parentTag));

			if ($vendor_id)
			{
				$paymentDetails = array();

				foreach ($params as $key => $param)
				{
					foreach ($param as $key => $value)
					{
						if ($key != "payment_gateways" && $param->payment_gateways == $payment_gateway)
						{
							{
								$form->setValue($key, '', $value);
							}
						}
					}
				}
			}

			$fieldSet = $form->getFieldset('payment_gateway');
			$html = array();

			foreach ($fieldSet as $field)
			{
				if ($app->isAdmin())
				{
					$html[] = $field->renderField();
				}
				else
				{
					// To convert frontend subform in Bootstrap 3
					$tempForm = str_replace('control-group', 'col-xs-12 col-sm-6 form-group form_'
					. $field->class, $field->renderField(array('hiddenLabel' => false))
					);
					$col = str_replace('control-label', 'col-xs-12 col-md-3', $tempForm);
					$col = str_replace('controls', 'col-xs-12 col-md-8', $col);
					$html[] = $col;
				}
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
	 * @return boolean
	 */
	public function save($data)
	{
		$table  = $this->getTable();
		$db     = JFactory::getDbo();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$layout = $input->get('layout', '', 'STRING');
		$tjvendorFrontHelper = new TjvendorFrontHelper;

		JLoader::import('components.com_tjvendors.events.vendor', JPATH_SITE);
		$tjvendorsTriggerVendor = new TjvendorsTriggerVendor;

		if (!$user->authorise('core.admin'))
		{
			$vendor_id = $tjvendorFrontHelper->getvendor();

			if ($vendor_id)
			{
				if ($user->authorise('core.edit.own', 'com_tjvendors'))
				{
					if ($user->id == $data['user_id'] && $vendor_id == $data['vendor_id'])
					{
						$authorised = true;
					}
					else
					{
						$authorised = false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				$authorised = $user->authorise('core.create', 'com_tjvendors');
			}

			if ($authorised !== true)
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		foreach ($data['payment_gateway'] as $key => $value)
		{
			if (sizeof($value) <= 1)
			{
				unset($data['payment_gateway'][$key]);
			}
		}

		$paymentGatway['payment_gateway'] = $data['payment_gateway'];
		$data['params'] = json_encode($paymentGatway);

		// To check if editing in registration form
		if ($data['vendor_id'])
		{
			$table->save($data);
			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$vendorClients = $tjvendorFrontHelper->getClientsForVendor($data['vendor_id']);
			$count = 0;

			// Check if the vendor exists for another client
			foreach ($vendorClients as $client)
			{
				if ($client == $data['vendor_client'])
				{
					$count++;
				}
			}

			// If no client present then vendor registers for first time  for a client
			if ($count == 0)
			{
				$client_entry = new stdClass;
				$client_entry->client = $data['vendor_client'];
				$client_entry->vendor_id = $data['vendor_id'];
				$client_entry->params = $data['params'];
				$client_entry->approved = $data['approved'];

				// Insert the object into the user profile table.
				JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
				$tjvendorsTriggerVendor->onAfterVendorSave($data, true);

				return true;
			}
			else
			{
				$query = $db->getQuery(true);

				// Fields to update.
				if (isset($data['params']))
				{
					$fields = array(
						$db->quoteName('params') . ' = ' . $db->quote($data['params'])
					);
				}
				else
				{
					$fields = array(
					$db->quoteName('approved') . ' = ' . $db->quote($data['approved']),
					);
				}

				// The vendor information is updated for that client
					$conditions = array(
					$db->quoteName('vendor_id') . ' = ' . $db->quote($data['vendor_id']),
					$db->quoteName('client') . ' = ' . $db->quote($data['vendor_client'])
				);

				$query->update($db->quoteName('#__vendor_client_xref'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$result = $db->execute();

				/* Trigger on Vendor Edit / update*/
				$tjvendorsTriggerVendor->onAfterVendorSave($data, false);

				return true;
			}
		}
		else
		{
			// Vendor registers for the first time for a client
			if ($table->save($data) === true)
			{
				$data['vendor_id'] = $table->vendor_id;

				if (!empty($data['vendor_client']))
				{
					$client_entry = new stdClass;
					$client_entry->client = $data['vendor_client'];
					$client_entry->vendor_id = $data['vendor_id'];
					$client_entry->params = $data['params'];
					$client_entry->approved = $data['approved'];

					// Insert the object into the vendor_client_xref table.
					$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $client_entry);
				}

				/* Send mail on vendor creation */
				$tjvendorsTriggerVendor->onAfterVendorSave($data, true);

				return $data['vendor_id'];
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

	/**
	 * Method to format payment config json structure, As we are taking data in subform and saving it in params, format the JSON structure
	 *
	 * @param   array  $data            Jform processed data
	 * @param   array  $paymentDetails  Current data of payment gateway
	 *
	 * @return object The formatted json structure
	 */
	public function formatPaymentStructure($data, $paymentDetails)
	{
		$client = $this->getClient();
		$tjvendorFrontHelper = new TjvendorFrontHelper;
		$vendorDetails = $tjvendorFrontHelper->getPaymentDetails($data['vendor_id'], $client);

		// Object  of old payment gateway params
		$oldParams = json_decode($vendorDetails->params);

		if (is_string($data['gateway']))
		{
			$oldParams->{$data['gateway']} = $paymentDetails;
		}

		return $oldParams;
	}
}
