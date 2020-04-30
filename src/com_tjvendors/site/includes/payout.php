<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Payout class.
 *
 * This class hold the property of the vendor payout entity and perform the appropriate operations
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsPayout extends CMSObject
{
	/**
	 * primary key of the payout record.
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	public $id = 0;

	/**
	 * primary key of the vendor table
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $vendor_id = 0;

	/**
	 * Currency.
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $currency = '';

	/**
	 * Total amount.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $total = 0;

	/**
	 * Credit amount.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $credit = 0;

	/**
	 * Debited amount
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $debit = 0;

	/**
	 * Reference order id after payout given.
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $reference_order_id = '';

	/**
	 * Transaction time.
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $transaction_time = '';

	/**
	 * Integrated component client name eg. com_tjlms, com_jticketing, com_jgive
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $client = '';

	/**
	 * Transaction Id after payout processed.
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $transaction_id = '';

	/**
	 * Status of the passbook record
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $status = 1;

	/**
	 * Hold the other required information in the JSON format
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $params = '';

	/**
	 * Holds the already loaded instances of the passbook
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $payoutObj = array();

	/**
	 * Constructor activating the default information of the payout
	 *
	 * @param   int  $id  The unique passbook id to load.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}
	}

	/**
	 * Returns the global passbook object
	 *
	 * @param   integer  $id  The primary key of the passbook to load (optional).
	 * 
	 * @return  TjvendorsVendor  The vendor object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getInstance($id = 0)
	{
		if (!$id)
		{
			return new TjvendorsPayout;
		}

		if (empty(self::$payoutObj[$id]))
		{
			self::$payoutObj[$id] = new TjvendorsPayout($id);
		}

		return self::$payoutObj[$id];
	}

	/**
	 * Method to load a passbook properties
	 *
	 * @param   int  $id  The Passbook id whose properties need to load while creating object
	 *
	 * @return  boolean  True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function load($id)
	{
		$table = TJVendors::table("payout");

		if (!$table->load($id))
		{
			return false;
		}

		$this->id                 = (int) $table->get('id');
		$this->vendor_id          = (int) $table->get('vendor_id');
		$this->currency           = $table->get('currency');
		$this->total              = $table->get('total');
		$this->credit             = $table->get('credit');
		$this->debit              = $table->get('debit');
		$this->reference_order_id = $table->get('reference_order_id');
		$this->transaction_time   = $table->get('transaction_time');
		$this->client             = $table->get('client');
		$this->transaction_id     = $table->get('transaction_id');
		$this->status             = $table->get('status');
		$this->params             = $table->get('params');

		$this->setClient($client);

		return true;
	}

	/**
	 * Method to return all the key and values of all properties
	 *
	 * @param   boolean  $public  If true, returns only the public properties.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProperties($public = true)
	{
		return get_object_vars($this);
	}

	/**
	 * Method to get the id
	 *
	 * @return  integer return the id.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Method to get the vendor id
	 *
	 * @return  integer return the vendor id.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getVendorId()
	{
		return $this->vendor_id;
	}

	/**
	 * Method to get the currency
	 *
	 * @return  string return the currency
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * Method to get the total amount
	 *
	 * @return  double return the total
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Method to get the credit amount
	 *
	 * @return  double return the credit
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * Method to get the debit amount
	 *
	 * @return  double return the debit
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getDebit()
	{
		return $this->debit;
	}

	/**
	 * Method to get the reference order id
	 *
	 * @return  string return the reference order id
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getReferenceOrderId()
	{
		return $this->reference_order_id;
	}

	/**
	 * Method to get the transaction time
	 *
	 * @return  string return the transaction time
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getTransactionTime()
	{
		return $this->transaction_time;
	}

	/**
	 * Method to get the client
	 *
	 * @return  string return the client
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Method to get the transaction_id
	 *
	 * @return  string return the transaction_id
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getTransactionId()
	{
		return $this->transaction_id;
	}

	/**
	 * Method to get the status
	 *
	 * @return  integer return the status
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Method to get the params
	 *
	 * @return  string return the params
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Function for adding credit entry
	 *
	 * @param   array  $orderData  Order data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addEntry($orderData)
	{
		$clientParams     = ComponentHelper::getParams($orderData['client']);
		$currency         = $clientParams->get('currency');
		$vendorParams     = TJVendors::config();
		$payoutDayLimit   = $vendorParams->get('payout_limit_days', 0, 'INT');
		$date             = Factory::getDate();
		$payoutDayLimit   = $date->modify("-" . $payoutDayLimit . " day");
		$checkOrderPayout = false;
		$payoutTable = Table::getInstance('payout', 'TjvendorsTable', array());
		$payoutTable->load(array('reference_order_id' => $orderData['order_id']));

		if ($payoutTable->debit > 0)
		{
			$checkOrderPayout = $payoutTable->order_id;
		}

		$entryData['vendor_id'] = $orderData['vendor_id'];
		$totalAmount = TjvendorsHelper::getTotalAmount($entryData['vendor_id'], $currency, $orderData['client']);
		$entryData['reference_order_id'] = $orderData['order_id'];
		$entryData['transaction_id']     = $orderData['client_name'] . '-' . $currency . '-' . $entryData['vendor_id'] . '-';
		$entryData['transaction_time']   = $date->toSql();

		if ($orderData['status'] != "C")
		{
			if ($orderData['status'] == "RF")
			{
				$entry_status = "debit_refund";
			}
			elseif ($orderData['status'] == "P")
			{
				$entry_status = "debit_pending";
			}

			$entryData['debit']  = $orderData['amount'] - $orderData['fee_amount'];
			$entryData['credit'] = '0.00';
			$entryData['total']  = $totalAmount['total'] - $entryData['debit'];
		}
		else
		{
			$entryData['debit']  = 0;
			$entryData['credit'] = $orderData['amount'] - $orderData['fee_amount'];
			$entryData['total']  = $totalAmount['total'] + $entryData['credit'];
			$entry_status        = "credit_for_ticket_buy";
		}

		$params = array("customer_note" => $orderData['customer_note'], "entry_status" => $entry_status);
		$entryData['params']   = json_encode($params);
		$entryData['currency'] = $currency;
		$entryData['client']   = $orderData['client'];
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'payout');
		$tjvendorsModelPayout = BaseDatabaseModel::getInstance('Payout', 'TjvendorsModel');
		$vendorDetail = $tjvendorsModelPayout->addCreditEntry($entryData);
	}
}
