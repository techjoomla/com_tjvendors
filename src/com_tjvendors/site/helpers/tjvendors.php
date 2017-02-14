<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class TjvendorsFrontendHelper
 *
 * @since  1.6
 */
class TjvendorsHelpersTjvendors
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
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = $db->quoteName(array('vendors.vendor_client','vendors.vendor_id'));
		$columns[0] = 'DISTINCT' . $columns[0];
		$query->select($columns);
		$query->from($db->quoteName('#__tjvendors_vendors', 'vendors'));
		$query->where($db->quoteName('vendors.user_id') . ' = ' . $user_id);
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		$uniqueClient[] = JText::_('JFILTER_PAYOUT_CHOOSE_CLIENT');
		$uniqueClient[] = array("vendor_client" => "All Clients","client_value" => 0);

		foreach ($rows as $row)
		{
			$uniqueClient[] = array("vendor_client" => $row['vendor_client'], "client_value" => $row['vendor_id']);
		}

		return $uniqueClient;
	}

	/**
	 * Get array of pending payout amount
	 *
	 * @param   integer  $vendor_id  required to give vendor specific result
	 * 
	 * @param   integer  $user_id    required to give user specific result
	 *   
	 * @return $totalDetails|array
	 */
	public static function getTotalDetails($vendor_id,$user_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$subQuery = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tjvendors_passbook'));

		if ($vendor_id == 0)
		{
			$subQuery->select('vendor_id');
			$subQuery->from($db->quoteName('#__tjvendors_vendors'));
			$subQuery->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id));
			$query->where($db->quoteName('vendor_id') . ' IN (' . $subQuery . ')');
			$query->order($db->quoteName('vendor_id') . ' ASC');
		}
		else
		{
		$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		$db->setQuery($query);
		$rows = $db->loadAssocList();
		$totalDebitAmount = 0;
		$totalCreditAmount = 0;
		$totalpendingAmount = 0;

		foreach ($rows as $row)
		{
			$totalDebitAmount = $totalDebitAmount + $row['debit'];
			$totalCreditAmount = $totalCreditAmount + $row['credit'];
			$totalpendingAmount = $totalCreditAmount - $totalDebitAmount;
		}

		$totalDetails = array("debitAmount" => $totalDebitAmount,"creditAmount" => $totalCreditAmount,"pendingAmount" => $totalpendingAmount);

		return $totalDetails;
	}

	/**
	 * Get array of currency
	 *
	 * @return null|object
	 */
	public static function getCurrency()
	{
		$currencies = JFactory::getApplication()->input->get('currency', '', 'ARRAY');
		$currUrl = "";
		$currencies = (array) $currencies;

		foreach ($currencies as $currency)
		{
			$currUrl .= "&currency[]=" . $currency;
		}

		return $currUrl;
	}
}
