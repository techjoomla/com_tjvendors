<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * List model for Affiliate Logs
 *
 * @package  TJVendors
 *
 * @since    __DEPLOY_VERSION__
 */
class TjvendorsModelAffiliateLogs extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelLegacy
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'vendor_commission', 'aff.commission',
				'user_commission', 'aff.user_commission',
				'code', 'aff.code',
				'ip', 'a.ip',
				'user_id', 'a.user_id',
				'state', 'a.state'
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
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "desc");
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getListQuery()
	{
		$user = Factory::getUser();

		$vendorDetails = Table::getInstance('vendor', 'TjvendorsTable', array());
		$vendorDetails->load(array('user_id' => $user->id));

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*, a.ip as ip, aff.code, aff.commission as vendor_commission, aff.user_commission'
		);

		$query->from($db->qn('#__affiliate_log', 'a'));

		$query->join('INNER', $db->qn('#__affiliates', 'aff') .
				' ON (' . $db->quoteName('aff.id') . ' = ' . $db->quoteName('a.affiliate_id') . ')');

		if ($vendorDetails->vendor_id)
		{
			$query->where($db->qn('aff.vendor_id') . ' = ' . (int) $vendorDetails->vendor_id);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('aff.code LIKE ' . $search);
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "desc");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}
}
