<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JLoader::import('payout', JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
JLoader::import('tjvendors', JPATH_ADMINISTRATOR . '/components/com_tjvendors/helpers');

/**
 * Class TjvendorsFrontendHelper
 *
 * @since  1.6
 */
class TjvendorFrontHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_tjvendors/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_tjvendors/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'TjvendorsModel');
		}

		return $model;
	}

	/**
	 * Get array of unique Clients
	 *
	 * @param   string  $user_id  To give user specific clients for the filter
	 *
	 * @return null|object
	 */
	public static function getUniqueClients($user_id)
	{
		$vendor_id = self::getvendor();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT' . $db->quoteName('client'));
		$query->from($db->quoteName('#__tjvendors_passbook', 'vendors'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendors.vendor_id') . ' = ' . $vendor_id);
		}

		$db->setQuery($query);
		$clients[] = JText::_('JFILTER_PAYOUT_CHOOSE_CLIENTS');

		$result = $db->loadAssocList();

		foreach ($result as $i)
		{
			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$client = $tjvendorFrontHelper->getClientName($i['client']);
			$client = JText::_('COM_TJVENDORS_VENDOR_CLIENT_' . strtoupper($i['client']));
			$clients[] = $client;
		}

		return $clients;
	}

	/**
	 * Get paid amount
	 *
	 * @param   string  $vendor_id  integer
	 *
	 * @param   string  $client     client
	 *
	 * @param   string  $currency   integer
	 *
	 * @return amount
	 */
	public static function getTotalDetails($vendor_id, $client, $currency)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('sum(' . $db->quoteName('credit') . ') As credit');
		$query->select('sum(' . $db->quoteName('debit') . ') As debit');

		$query->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . '=' . $vendor_id);
		}

		if (!empty($client))
		{
		$query->where($db->quoteName('client') . " = " . $db->quote($client));
		}

		if (!empty($currency))
		{
		$query->where($db->quoteName('currency') . " = " . $db->quote($currency));
		}

		$db->setQuery($query);
		$rows = $db->loadAssoc();
		$totalDebitAmount = $rows['debit'];
		$totalCreditAmount = $rows['credit'];
		$totalpendingAmount = $totalCreditAmount - $totalDebitAmount;

		$totalDetails = array("debitAmount" => $totalDebitAmount, "creditAmount" => $totalCreditAmount, "pendingAmount" => $totalpendingAmount);

		return $totalDetails;
	}

	/**
	 * Get clients for vendors
	 *
	 * @param   integer  $vendor_id  required to give vendor specific result
	 *
	 * @return clientsForVendor|array
	 */
	public static function getClientsForVendor($vendor_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__vendor_client_xref'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $vendor_id);
		}

		$db->setQuery($query);
		$result = $db->loadAssocList();

		if (!empty($result))
		{
			foreach ($result as $client)
			{
				$clientsForVendor[] = $client['client'];
			}

			return $clientsForVendor;
		}
	}

	/**
	 * Get vendor for that user
	 *
	 * @return vendor
	 */
	public static function getvendor()
	{
		$user_id = JFactory::getuser()->id;
		$vendorDetails = JTable::getInstance('vendor', 'TjvendorsTable', array());
		$vendorDetails->load(array('user_id' => $user_id));

		return $vendorDetails->vendor_id;
	}

	/**
	 * Get vendor for that user
	 *
	 * @return vendor
	 */
	public static function getCurrencies()
	{
		$vendor_id = self::getvendor();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT' . $db->quoteName('currency'));
		$query->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $vendor_id);
		}

		$db->setQuery($query);
		$currencies[] = JText::_('JFILTER_PAYOUT_CHOOSE_CURRENCY');

		$result = $db->loadAssocList();

		foreach ($result as $i)
		{
			$currencies[] = $i;
		}

		return $currencies;
	}

	/**
	 * Get get paymentDetails
	 *
	 * @param   string  $vendor_id  integer
	 *
	 * @param   string  $client     integer
	 *
	 * @return res|integer
	 */
	public static function getPaymentDetails($vendor_id, $client)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__vendor_client_xref'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		if (!empty($client))
		{
			$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		}

		$db->setQuery($query);
		$res = $db->loadObject();

		return $res;
	}

	/**
	 * Check if the logged in user is a vendor depending on the client
	 *
	 * @param   integer  $user_id  user id
	 *
	 * @param   string   $client   client
	 *
	 * @return   mixed
	 *
	 * @since   1.1
	 */
	public static function checkVendor($user_id, $client)
	{
		if (empty($user_id))
		{
			$user_id = jFactory::getuser()->id;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('v.vendor_id'));
		$query->from($db->quoteName('#__tjvendors_vendors', 'v'));
		$query->join('LEFT', $db->quoteName('#__vendor_client_xref', 'vx') .
		' ON (' . $db->quoteName('v.vendor_id') . ' = ' . $db->quoteName('vx.vendor_id') . ')');
		$query->where($db->quoteName('v.user_id') . ' = ' . $db->quote($user_id));
		$query->where($db->quoteName('vx.client') . ' = ' . $db->quote($client));
		$db->setQuery($query);
		$vendor = $db->loadResult();

		if (!$vendor)
		{
			return false;
		}
		else
		{
			return $vendor;
		}
	}

	/**
	 * function for geting paymentgateway details
	 *
	 * @param   integer  $userId  user id
	 *
	 * @param   string   $client  client
	 *
	 * @return  boolean
	 *
	 * @since   1.1
	 */
	public function checkGatewayDetails($userId, $client)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$arrayColumns = array('vc.params');
		$query->select($db->quoteName($arrayColumns));
		$query->from($db->quoteName('#__tjvendors_vendors', 'v'));
		$query->join('LEFT', $db->quoteName('#__vendor_client_xref', 'vc') .
		' ON (' . $db->quoteName('v.vendor_id') . ' = ' . $db->quoteName('vc.vendor_id') . ')');
		$query->where($db->quoteName('v.user_id') . ' = ' . $db->quote($userId));
		$query->where($db->quoteName('vc.client') . ' = ' . $db->quote($client));
		$db->setQuery($query);
		$result = $db->loadAssoc();
		$params = json_decode($result['params']);

		if (empty($params->payment_email_id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * function for adding credit entry
	 *
	 * @param   integer  $order_data  integer
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addEntry($order_data)
	{
		$com_params = JComponentHelper::getParams($order_data['client']);
		$vendorParams = JComponentHelper::getParams('com_tjvendors');
		$payout_day_limit = $vendorParams->get('payout_limit_days', '0', 'INT');
		$date = JFactory::getDate();
		$payout_date_limit = $date->modify("-" . $payout_day_limit . " day");
		$currency = $com_params->get('currency');

		$payoutTable = JTable::getInstance('payout', 'TjvendorsTable', array());
		$payoutTable->load(array('reference_order_id' => $order_data['order_id']));

		if ($payoutTable->debit > 0)
		{
			$checkOrderPayout = $payoutTable->order_id;
		}
		else
		{
			$checkOrderPayout = false;
		}

		$entry_data['vendor_id'] = $order_data['vendor_id'];
		$totalAmount = TjvendorsHelpersTjvendors::getTotalAmount($entry_data['vendor_id'], $currency, $order_data['client']);
		$entry_data['reference_order_id'] = $order_data['order_id'];
		$entry_data['transaction_id'] = $order_data['client_name'] . '-' . $currency . '-' . $entry_data['vendor_id'] . '-';
		$entry_data['transaction_time'] = JFactory::getDate()->toSql();

		if ($order_data['status'] != "C")
		{
			if ($order_data['status'] == "RF")
			{
				$entry_status = "debit_refund";
			}
			elseif ($order_data['status'] == "P")
			{
				$entry_status = "debit_pending";
			}

			$entry_data['debit'] = $order_data['amount'] - $order_data['fee_amount'];
			$entry_data['credit'] = '0.00';
			$entry_data['total'] = $totalAmount['total'] - $entry_data['debit'];
		}

		elseif ($order_data['status'] == "C")
		{
			$entry_data['credit'] = $order_data['amount'] - $order_data['fee_amount'];
			$entry_data['debit'] = 0;
			$entry_data['total'] = $totalAmount['total'] + $entry_data['credit'];
			$entry_status = "credit_for_ticket_buy";
		}

		$params = array("customer_note" => $order_data['customer_note'],"entry_status" => $entry_status);
		$entry_data['params'] = json_encode($params);
		$entry_data['currency'] = $currency;
		$entry_data['client'] = $order_data['client'];
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'payout');
		$tjvendorsModelPayout = JModelLegacy::getInstance('Payout', 'TjvendorsModel');
			$vendorDetail = $tjvendorsModelPayout->addCreditEntry($entry_data);
	}

	/**
	 * Get paid amount
	 *
	 * @param   string  $vendor_id     integer
	 *
	 * @param   string  $currency      integer
	 *
	 * @param   string  $filterClient  client from filter
	 *
	 * @return amount
	 */
	public static function getPaidAmount($vendor_id,$currency,$filterClient)
	{
		$input = JFactory::getApplication()->input;
		$urlClient = $input->get('client', '', 'STRING');
		$com_params = JComponentHelper::getParams('com_tjvendors');
		$bulkPayoutStatus = $com_params->get('bulk_payout');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($filterClient))
		{
			$client = $filterClient;
		}
		else
		{
			$client = $urlClient;
		}

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		if (!empty($currency))
		{
			$query->where($db->quoteName('currency') . ' = ' . $db->quote($currency));
		}

		if ($bulkPayoutStatus == 0)
		{
			$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		}

		$db->setQuery($query);
		$paidDetails = $db->loadAssocList();
		$amount = 0;

		foreach ($paidDetails as $detail)
		{
			$entryStatus = json_decode($detail['params']);
			$entryStatus->entry_status;

			if ($entryStatus->entry_status == "debit_payout" && $detail['status'] == 1)
			{
				$amount = $amount + $detail['debit'];
			}
		}

		return $amount;
	}

	/**
	 * Get client name 
	 *
	 * @param   string  $client  client
	 *
	 * @return  $clientName
	 */
	public static function getClientName($client)
	{
		$clientName = strtoupper($client);

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
		$lang->load($client, JPATH_ADMINISTRATOR, null, false, true);

		return JText::_($clientName);
	}
}
