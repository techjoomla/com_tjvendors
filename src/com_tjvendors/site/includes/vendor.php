<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/**
 * Vendor class.
 *
 * This class hold the property of the vendor entity and perform the appropriate operations
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsVendor extends CMSObject
{
	/**
	 * primary key of the vendor record.
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	public $vendor_id = 0;

	/**
	 * primary key of the joomla user
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $user_id = 0;

	/**
	 * The name of the vendor.
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $vendor_title = '';

	/**
	 * Unique string representation of the vendor
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $alias = '';

	/**
	 * Vendor description
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $vendor_description = '';

	/**
	 * The path of the vendo logo
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $vendor_logo = '';

	/**
	 * State of the vendor
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $state = 0;

	/**
	 * Orderign of the record.
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $ordering = 0;

	/**
	 * Joomla user id who is currentaly checked out the item
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $checked_out = 0;

	/**
	 * Timestamp when the user checked out the record
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $checked_out_time = '';

	/**
	 * Hold the other required information in the JSON format
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $params = '';

	/**
	 * Integrated component client name eg. com_tjlms, com_jticketing
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $client = '';

	/**
	 * Is vendor approved by the client
	 * By default the vendor is approved
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $approved = 1;

	/**
	 * Payment gateway details configured by the vendor
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $payment_gateway = '';

	/**
	 * holds the already loaded instances of the vendor
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $vendorObj = array();

	/**
	 * Constructor activating the default information of the vendor
	 *
	 * @param   int     $id      The unique vendor id to load.
	 * @param   string  $client  the client name whose properties need to load while creating object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($id = 0, $client = '')
	{
		if (!empty($id))
		{
			$this->load($id, $client);
		}
		else
		{
			$this->checked_out_time = Factory::getDbo()->getNullDate();
		}
	}

	/**
	 * Returns the global vendor object
	 *
	 * @param   integer  $id      The primary key of the vendor to load (optional). if id is empty the empty instance will be returned
	 * @param   string   $client  the client name whose properties need to load while creating object
	 *
	 * @return  TjvendorsVendor  The vendor object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getInstance($id = 0, $client = '')
	{
		if (!$id)
		{
			return new TjvendorsVendor;
		}

		// Check if the coupon id is already cached.
		if (empty(self::$vendorObj[$id]))
		{
			self::$vendorObj[$id] = new TjvendorsVendor($id, $client);
		}

		return self::$vendorObj[$id];
	}

	/**
	 * Method to load a vendor properties
	 *
	 * @param   int     $id      The vendor id
	 * @param   string  $client  the client name whose properties need to load while creating object
	 *
	 * @return  boolean  True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function load($id, $client = '')
	{
		$table = TJVendors::table("vendor");

		if (!$table->load($id))
		{
			return false;
		}

		// Check for the client in the xref
		$xreftable = TJVendors::table("vendorclientxref");

		if (!$xreftable->load(array('vendor_id' => $id, 'client' => $client)))
		{
			return false;
		}

		$this->vendor_id           = (int) $table->get('vendor_id');
		$this->user_id             = (int) $table->get('user_id');
		$this->vendor_title        = $table->get('vendor_title');
		$this->alias               = $table->get('alias');
		$this->vendor_description  = $table->get('vendor_description');
		$this->vendor_logo         = $table->get('vendor_logo');
		$this->state               = (int) $table->get('state');
		$this->ordering            = (int) $table->get('ordering');
		$this->checked_out         = (int) $table->get('checked_out');
		$this->checked_out_time    = $table->get('checked_out_time');
		$this->params              = $table->get('params');
		$this->setClient($client);

		return true;
	}

	/**
	 * Method to load a vendor properties by using the joomla user id
	 *
	 * @param   int  $id  The joomla user id
	 *
	 * @return  TjvendorsVendor  The vendor object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function loadByUserId($id = null)
	{
		if (is_null($id))
		{
			$id = Factory::getUser()->id;
		}

		/** @var $table TjvendorsTableVendor */
		$table = TJVendors::table("vendor");

		if (!$table->load(array('user_id' => $id)))
		{
			// If no record return blank object
			return new TjvendorsVendor;
		}

		return self::getInstance($table->vendor_id);
	}

	/**
	 * Get the title of the vendor
	 *
	 * @return  String
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTitle()
	{
		return $this->vendor_title;
	}

	/**
	 * Get the description of the vendor
	 *
	 * @return  String
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDescription()
	{
		return $this->vendor_description;
	}

	/**
	 * Get the logo of the vendor
	 *
	 * @return  String
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLogo()
	{
		if (empty($this->vendor_logo))
		{
			return Uri::root() . "administrator/components/com_tjvendors/assets/images/default.png";
		}

		return Uri::root() . $this->vendor_logo;
	}

	/**
	 * Check whether the vendor is associated with the provided client
	 *
	 * @param   string  $client  The client name to check integration eg. com_jticketing, com_tjllms
	 *
	 * @return  bool True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isAssociatedToClient($client = '')
	{
		static $clientExist = null;

		if (!$this->vendor_id)
		{
			return false;
		}

		if ($clientExist[$client] != null)
		{
			return $clientExist[$client];
		}

		/** @var $table TjvendorsTableVendor */
		$table = TJVendors::table("vendor");
		$clientExist[$client] = $table->load(array('vendor_id' => $this->vendor_id, 'client' => $client));

		return $clientExist[$client];
	}

	/**
	 * Set client properties to the vendor object
	 *
	 * @param   string  $client  The client name to check integration eg. com_jticketing, com_tjllms
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setClient($client)
	{
		// Check for the client in the xref
		$xreftable = TJVendors::table("vendorclientxref");

		if (!$xreftable->load(array('vendor_id' => $this->vendor_id, 'client' => $client)))
		{
			return false;
		}

		$this->client      = $xreftable->get('client');
		$this->approved    = (int) $xreftable->get('approved');

		// State property override by the client
		$this->state       = (int) $xreftable->get('state');

		// Expecting RuntimeException thrown by the Joomla\Registry\Format\Json class
		try
		{
			// Params properties are overrided by the client params
			$clientParams = new Registry($xreftable->get('params'));
			$vendorParam  = new Registry($this->params);
			$vendorParam->merge($clientParams);
			$this->params      = $vendorParam->toString();

			if (!empty($this->params))
			{
				$this->payment_gateway = $vendorParam->get('payment_gateway');
			}
		}
		catch (Exception $e)
		{
			// Don't throw an error
		}

		return true;
	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProperties()
	{
		return get_object_vars($this);
	}
}
