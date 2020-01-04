<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Methods supporting a list of Tjvendors records.
 *
 * @since  1.6
 */
class TjvendorsModelPayouts extends ListModel
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
				'currency', 'pass.`currency`',
				'ordering', 'pass.`ordering`',
				'vendor_title', 'vendors.`vendor_title`',
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
		$app = Factory::getApplication('administrator');

		// Set ordering.
		$orderCol = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'vendors.vendor_id';
		}

		$this->setState('list.ordering', $orderCol);

		$vendorId = $app->getUserStateFromRequest($this->context . '.filter.vendor_id', 'vendor_id', '0', 'string');
		$this->setState('filter.vendor_id', $vendorId);

		$filterClient = $app->getUserStateFromRequest($this->context . '.filter.vendor_client', 'vendor_client', '', 'string');
		$urlClient = $app->input->get('client', '', 'STRING');

		if (empty($filterClient))
		{
			$filterClient = $urlClient;
		}

		$this->setState('filter.vendor_client', $filterClient);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_tjvendors');
		$this->setState('params', $params);

		$this->setState('list.limit', '0');

		// List state information.
		parent::populateState('vendors.vendor_id', 'asc');
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
		$input = Factory::getApplication()->input;
		$vendor_id = $input->get('vendor_id', '', 'INTEGER');
		$urlClient = $input->get('client', '', 'STRING');
		$filterClient = $this->getState('filter.vendor_client');
		$com_params = ComponentHelper::getParams('com_tjvendors');
		$payout_day_limit = $com_params->get('payout_limit_days', '0', 'INT');
		$date = Factory::getDate();
		$payout_date_limit = $date->modify("-" . $payout_day_limit . " day");
		$bulkPayoutStatus = $com_params->get('bulk_payout');
		$vendor = $this->getState('filter.vendor_id');

		if (!empty($urlClient))
		{
			$component_params = ComponentHelper::getParams($urlClient);
			$com_currency = $component_params->get('currency');
		}

		$db = Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select(array('vendors.vendor_id', 'vendors.vendor_title', 'pass.*'));
		$query->from($db->quoteName('#__tjvendors_vendors', 'vendors'));
		$query->join('LEFT', $db->quoteName('#__tjvendors_passbook', 'pass') .
			' ON (' . $db->quoteName('vendors.vendor_id') . ' = ' . $db->quoteName('pass.vendor_id') . ')');
		$query->where($db->quoteName('pass.id') . ' is not null');

		if (!empty($payout_date_limit))
		{
			$query->where($db->quoteName('pass.transaction_time') . ' <= ' . $db->quote($payout_date_limit));
		}

		if ($filterClient != '0')
		{
			$client = $filterClient;
		}
		else
		{
			$client = $urlClient;
		}

		if ($bulkPayoutStatus == 0)
		{
			if (!empty($client))
			{
				$query->where($db->quoteName('pass.client') . ' = ' . $db->quote($client));
			}
		}

		// Display according to filter

		if (empty($vendor))
		{
			if (!empty($vendor_id))
			{
				$query->where($db->quoteName('pass.vendor_id') . '=' . $vendor_id);
			}
		}
		else
		{
			$query->where($db->quoteName('pass.vendor_id') . ' = ' . $db->quote($vendor));
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('pass.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(' . $db->quoteName('pass.currency') . ' LIKE ' . $search .
							'OR ' . $db->quoteName('pass.vendor_id') . ' LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC')))
		{
			$orderDirn = 'DESC';
		}

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
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

		// Get latest payout amount for provided vendor and currency
		foreach ($items as $i => $item)
		{
			foreach ($items as $j => $tempItem)
			{
				if (($item->vendor_id == $tempItem->vendor_id) && ($item->currency == $tempItem->currency))
				{
					if ($item->id > $tempItem->id)
					{
						unset($items[$j]);
					}
				}
			}
		}

		return $items;
	}
}
