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
class TjvendorsModelPayouts extends JModelList
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

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tjvendors');
		$this->setState('params', $params);

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
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('vendors.vendor_id','fees.currency','vendors.vendor_title','pass.*',)));
		$query->from($db->quoteName('#__tjvendors_vendors', 'vendors'));
		$query->join('LEFT', $db->quoteName('#__tjvendors_fee', 'fees') .
			' ON (' . $db->quoteName('vendors.vendor_id') . ' = ' . $db->quoteName('fees.vendor_id') . ')');
		$query->join('LEFT', $db->quoteName('#__tjvendors_passbook', 'pass') .
			' ON (' . $db->quoteName('fees.vendor_id') . ' = ' . $db->quoteName('pass.vendor_id') .
			' AND ' . $db->quoteName('fees.currency') . ' = ' . $db->quoteName('pass.currency') . ')');
		$query->where($db->quoteName('pass.id') . ' is not null');

		$client = $this->getState('filter.vendor_client');

		if (!empty($client))
		{
		$query->where($db->quoteName('vendors.vendor_client') . " = " . $db->quote($client));
		}

		$db->setQuery($query);
		$rows = $db->loadAssocList();

		// Filter by search in title
		$search = $this->getState('filter.search');

		// Filter vendor id
		$vendor = $this->getState('filter.vendor_id');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('pass.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(' . $db->quoteName('vendors.vendor_title') . ' LIKE ' . $search .
							'OR ' . $db->quoteName('fees.currency') . ' LIKE' . $search .
							'OR ' . $db->quoteName('vendors.vendor_client') . ' LIKE' . $search .
							'OR ' . $db->quoteName('pass.vendor_id') . ' LIKE' . $search . ')');
			}
		}

		// Display according to filter
		$vendor = $this->getState('filter.vendor_id');

		if (!empty($vendor))
		{
			$query->where($db->quoteName('vendors.vendor_id') . '=' . $vendor);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

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
