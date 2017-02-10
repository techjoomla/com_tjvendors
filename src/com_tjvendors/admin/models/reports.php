<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla  <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Tjvendors records.
 *
 * @since  1.6
 */
class TjvendorsModelReports extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'pass.`id`',
				'total', 'pass.`total`',
				'currency', 'fees.`currency`',
				'vendor_title', 'vendors.`vendor_title`',
				'client', 'vendors.`vendor_client`',
				'transaction_id', 'pass.`transaction_id`',
				'transaction_time', 'pass.`transaction_time`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Set ordering.
		$orderCol = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'vendors.vendor_id';
		}

		$this->setState('list.ordering', $orderCol);

		$vendorId = $app->getUserStateFromRequest($this->context . '.filter.vendor_id', 'vendor_id', '0', 'string');
		$this->setState('filter.vendor_id', $vendorId);

		$client = $app->getUserStateFromRequest($this->context . '.filter.vendor_client', 'vendor_client', '0', 'string');
		$this->setState('filter.vendor_client', $client);

		$transactionType = $app->getUserStateFromRequest($this->context . '.filter.transactionType', 'transactionType', '0', 'string');
		$this->setState('filter.transactionType', $transactionType);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tjvendors');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('vendors.vendor_id', 'asc');

		return $vendorId;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */

	public function getListQuery()
	{
		$db = JFactory::getDbo();
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'payouts');
		$TjvendorsModelPayouts = JModelLegacy::getInstance('Payouts', 'TjvendorsModel');
		$query = $PayoutsDetail = $TjvendorsModelPayouts->getListQuery();
		$transactionType = $this->getState('filter.transactionType', '');

		if (!empty($transactionType))
		{
			if ($transactionType == "debit")
			{
				$query->where($db->quoteName('debit') . " >0 ");
			}
			else
			{
				$query->where($db->quoteName('credit') . " >0 ");
			}
		}

		return $query;
	}

	/**
	 * get the list items
	 *
	 * @return   items
	 *
	 * @since    1.6
	 */

	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
}
