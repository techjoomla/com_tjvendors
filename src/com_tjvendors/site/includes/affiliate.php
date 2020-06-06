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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

/**
 * Affiliate class.
 *
 * This class hold the property of the affiliates entity and perform the appropriate operations
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsAffiliate extends CMSObject
{
	/**
	 * primary key of the Affiliate record.
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	public $id = 0;

	/**
	 * primary key of the vendor record.
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $vendor_id = 0;

	/**
	 * Affiliate name
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $name = '';

	/**
	 * Unique affiliates code
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $code = '';

	/**
	 * Unique string representation of the affiliates
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $alias = '';

	/**
	 * Affiliate description
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $description = '';

	/**
	 * Affiliate Commission type
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $commission_type = 'percent';

	/**
	 * Affiliate commision
	 *
	 * @var    String
	 * @since  __DEPLOY_VERSION__
	 */
	private $commision = '';

	/**
	 * Affiliate user commision
	 *
	 * @var    Integer
	 * @since  __DEPLOY_VERSION__
	 */
	private $user_commision = '';

	/**
	 * Affiliate limit
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $affiliatess_limit = 0;

	/**
	 * Affiliate Per user limit
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $max_per_user = 0;

	/**
	 * Timestamp Affiliate valid from
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $valid_from = '';

	/**
	 * Timestamp Affiliate valid to
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $valid_to = '';

	/**
	 * Affiliate status
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $state = 0;

	/**
	 * Timestamp when the user checked out the record
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $checked_out_time = '';

	/**
	 * Joomla user id who is currentaly checked out the item
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $checked_out = 0;

	/**
	 * Affiliate created by
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $created_by = 0;

	/**
	 * Timestamp when Affiliate created
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $created_at = '';

	/**
	 * Affiliate modified by
	 *
	 * @var    int
	 * @since  __DEPLOY_VERSION__
	 */
	private $modified_by = 0;

	/**
	 * Timestamp when Affiliate modified at
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $modified_at = '';

	/**
	 * Affiliate params
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $params = array();

	/**
	 * holds the already loaded instances of the affiliates
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $affiliatesObj = array();

	/**
	 * Constructor activating the default information of the affiliates
	 *
	 * @param   int  $id  The unique affiliates id to load.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}
		else
		{
			$this->checked_out_time = Factory::getDbo()->getNullDate();
		}
	}

	/**
	 * Returns the global affiliates object
	 * Returns the global affiliates object
	 *
	 * @param   integer  $id  The primary key of the affiliates to load (optional). if id is empty the empty instance will be returned
	 *
	 * @return  TjvendorsAffiliate  The affiliates object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getInstance($id = 0)
	{
		if (!$id)
		{
			return new TjvendorsAffiliate;
		}
		// Check if the coupon id is already cached.
		if (empty(self::$affiliatesObj[$id]))
		{
			self::$affiliatesObj[$id] = new TjvendorsAffiliate($id);
		}

		return self::$affiliatesObj[$id];
	}

	/**
	 * Method to load a affiliates properties
	 *
	 * @param   int  $id  The affiliates id
	 *
	 * @return  boolean  True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function load($id)
	{
		$table = TJVendors::table("affiliates");

		if (!$table->load($id))
		{
			return false;
		}

		$this->id           		= (int) $table->get('id');
		$this->vendor_id           	= (int) $table->get('vendor_id');
		$this->name             	= $table->get('name');
		$this->code        			= $table->get('code');
		$this->alias               	= $table->get('alias');
		$this->description  		= $table->get('description');
		$this->commission_type 		= $table->get('commission_type');
		$this->commission         	= (float) $table->get('commission');
		$this->user_commission         	= (float) $table->get('user_commission');
		$this->affiliates_limit     	= (int) $table->get('affiliates_limit');
		$this->max_per_user         = $table->get('max_per_user');
		$this->valid_from        	= $table->get('valid_from');
		$this->valid_to        		= $table->get('valid_to');
		$this->state              	= (int) $table->get('state');
		$this->checked_out        	= (int) $table->get('checked_out');
		$this->checked_out_time 	= $table->get('checked_out_time');
		$this->created_by			= (int) $table->get('created_by');
		$this->created_at    		= $table->get('created_at');
		$this->modified_by    		= (int) $table->get('modified_by');
		$this->modified_at    		= $table->get('modified_at');
		$this->params              	= $table->get('params');

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

	/**
	 * Method to save the coachee object to the database
	 *
	 * @param   boolean  $updateOnly  flag to update the entry
	 *
	 * @return  boolean  True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function save($updateOnly = false)
	{
		// Create the widget table object
		/** @var $table TjvendorsTableaffiliates */
		$table = TJVendors::table("affiliates");
		$table->bind($this->getProperties());

		// Allow an exception to be thrown.
		try
		{
			// Check and store the object.
			if (! $table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Check if new record
			$isNew = empty($this->id);

			// Store the user data in the database
			if (! ($table->store()))
			{
				$this->setError($table->getError());

				return false;
			}
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$this->id = $table->id;

		return $this->id;
	}

	/**
	 * Method to bind an associative array of data to a coachee object
	 *
	 * @param   array  &$array  The associative array to bind to the object
	 *
	 * @return  boolean  True on success
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function bind(&$array)
	{
		if (empty($array))
		{
			$this->setError(Text::_('COM_TJCOACHING_EMPTY_DATA'));

			return false;
		}

		if (!empty($array['name']))
		{
			if (strlen($array['name']) > 0 && strlen(trim($array['name'])) == 0)
			{
				$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_NAME'));

				return false;
			}

			$this->name = $array['name'];
		}

		// Edit case
		if (!empty($array['code']))
		{
			if (strlen($array['code']) != 4)
			{
				$this->setError(Text::_('COM_TJVENDORS_AFFILIATE_FORM_LBL_CODE_DESC'));

				return false;
			}

			$affiliatesCode = $this->loadByCode($array['code']);

			if ($affiliatesCode->id && $array['id'] != $affiliatesCode->id)
			{
				$this->setError(Text::_('COM_TJVENDORS_AFFILIATE_FORM_MSG_DUPLICATE_CODE'));

				return false;
			}
		}

		if (!empty($array['commission_type']))
		{
			$this->commission_type = $array['commission_type'];
		}

		if (!empty($array['affiliates_limit']))
		{
			if ($array['affiliates_limit'] < 0)
			{
				$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_LIMIT'));

				return false;
			}

			$this->affiliates_limit = $array['affiliates_limit'];
		}

		if (!empty($array['user_commission']))
		{
			if ($array['user_commission'] < 0)
			{
				$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_USER_COMMISSION'));

				return false;
			}

			$this->user_commission = $array['user_commission'];
		}

		if (!empty($array['commission']))
		{
			if ($array['commission'] < 0)
			{
				$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_COMMISSION'));

				return false;
			}

			$this->commission = $array['commission'];
		}

		if (!empty($array['max_per_user']))
		{
			if ($array['max_per_user'] < 0)
			{
				$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_MAX_PER_USER'));

				return false;
			}

			$this->max_per_user = $array['max_per_user'];
		}

		$validFrom = Factory::getDate($array['valid_from']);
		$unixValidFrom = $validFrom->toUnix();
		$validTo = Factory::getDate($array['valid_to']);
		$unixValidTo = $validTo->toUnix();

		// Check code field contains only alphanumeric characters
		if (!preg_match('/^[a-zA-Z0-9]+$/', $array['code']))
		{
			$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_CODE'));

			return false;
		}

		// Check valid form and to fields
		if (($unixValidFrom > $unixValidTo) || ($unixValidTo < $unixValidTo))
		{
			$this->setError(Text::_('COM_TJVENDOR_AFFILIATE_INVALID_DATE'));

			return false;
		}

		$this->id           		= (int) $array['id'];
		$this->vendor_id           	= (int) $array['vendor_id'];
		$this->code        			= $array['code'];
		$this->alias               	= $array['alias'];
		$this->description  		= $array['description'];
		$this->valid_from        	= $array['valid_from'];
		$this->valid_to        		= $array['valid_to'];
		$this->state              	= (int) $array['state'];
		$this->params              	= $array['params'];

		// Make sure its an integer
		$this->id = (int) $this->id;

		return true;
	}

	/**
	 * Method to get the affiliate object using the code
	 * 
	 * @param   string  $affiliatesCode  Affiliate Code
	 * 
	 * @return  Object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function loadByCode($affiliatesCode)
	{
		$table  = TJVendors::table('affiliates');
		$table->load(array('code' => $affiliatesCode));

		if (!$table->id)
		{
			return false;
		}

		return Tjvendors::Affiliate($table->id);
	}
}
