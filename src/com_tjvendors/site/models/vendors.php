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

/**
 * Tjvendors model.
 *
 * @since  1.6
 */
class TjvendorsModelVendors extends JModelList
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
				'transaction_id', 'pass.`transaction_id`',
				'transaction_time', 'pass.`transaction_time`',
				'reference_order_id','pass.`reference_order_id`',
				'vendor_client','vendors.`vendor_client`',

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

		$this->setState('list.ordering', $orderCol);

		$transactionType = $app->getUserStateFromRequest($this->context . '.filter.transactionType', 'transactionType', '0', 'string');
		$this->setState('filter.transactionType', $transactionType);

		$fromDate = $app->getUserStateFromRequest($this->context . '.filter.fromDate', 'fromDates', '0', 'string');
		$this->setState('filter.fromDate', $fromDate);

		$toDate = $app->getUserStateFromRequest($this->context . '.filter.toDate', 'toDates', '0', 'string');
		$this->setState('filter.toDate', $toDate);

		$client = $app->getUserStateFromRequest($this->context . '.filter.vendor_client', 'vendor_client', '0', 'string');
		$this->setState('filter.vendor_client', $client);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tjvendors');
		$this->setState('params', $params);

		parent::populateState();
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */

	protected function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('vendors.vendor_id', 'vendors.vendor_client', 'pass.*', 'fees.currency');
		$query->select($db->quoteName($columns));
		$query->from($db->quoteName('#__tjvendors_vendors', 'vendors'));
		$query->join('LEFT', $db->quoteName('#__tjvendors_fee', 'fees') .
			' ON (' . $db->quoteName('vendors.vendor_id') . ' = ' . $db->quoteName('fees.vendor_id') . ')');
		$query->join('LEFT', $db->quoteName('#__tjvendors_passbook', 'pass') .
			' ON (' . $db->quoteName('fees.vendor_id') . ' = ' . $db->quoteName('pass.vendor_id') .
			' AND ' . $db->quoteName('fees.currency') . ' = ' . $db->quoteName('pass.currency') . ')');
		$query->where($db->quoteName('pass.id') . ' is not null');
		$db->setQuery($query);

		$rows = $db->loadAssocList();

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
				$query->where('(' . $db->quoteName('vendors.vendor_title') . ' LIKE ' . $search .
							'OR ' . $db->quoteName('fees.currency') . ' LIKE' . $search .
							'OR ' . $db->quoteName('vendors.vendor_client') . ' LIKE' . $search .
							'OR ' . $db->quoteName('pass.vendor_id') . ' LIKE' . $search . ')');
			}
		}

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

		$fromDate = $this->getState('filter.fromDate', '');
		$toDate = $this->getState('filter.toDate', '');

		if (!empty($fromDate))
		{
			$query->where($db->quoteName('transaction_time') . ' BETWEEN ' . "'$fromDate'" . 'AND' . "'$toDate'");
		}

		$client = $this->getState('filter.vendor_client', '');
		$user_id = JFactory::getUser()->id;

		// Display according to filter
		$client = $this->getState('filter.vendor_client');

		if ($client == '0')
		{
			$query->where($db->quoteName('vendors.user_id') . '=' . $user_id);
		}
		else
		{
			$query->where($db->quoteName('vendors.vendor_id') . '=' . $client);
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
	 * Get items data.
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
