<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright  2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Tjvendors helper.
 *
 * @since  1.6
 */
class TjvendorsHelpersTjvendors
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		$input = JFactory::getApplication()->input;
		$full_client = $input->get('client', '', 'STRING');
		$full_client = explode('.', $full_client);

		$component = $full_client[0];
		$eName = str_replace('com_', '', $component);
		$file = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component . '/helpers/' . $eName . '.php');

		if (file_exists($file))
		{
			require_once $file;

			$prefix = ucfirst(str_replace('com_', '', $component));

			$cName = $prefix . 'Helper';

			if (class_exists($cName))
			{
				if (is_callable(array($cName, 'addSubmenu')))
				{
					$lang = JFactory::getLanguage();

					// Loading language file from the administrator/language directory then
					// Loading language file from the administrator/components/*extension*/language directory
					$lang->load($component, JPATH_BASE, null, false, false)
					|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), null, false, false)
					|| $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
					|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), $lang->getDefault(), false, false);

					call_user_func(array($cName, 'addSubmenu'), $vName . (isset($section) ? '.' . $section : ''));
				}
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_tjvendors';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Get array of unique Clients
	 *  
	 * @return null|object
	 */
	public static function getUniqueClients()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = $db->quoteName('vendors.vendor_client');
		$query->select('distinct' . $columns);
		$query->from($db->quoteName('#__tjvendors_vendors', 'vendors'));
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		$uniqueClient[] = JText::_('JFILTER_PAYOUT_CHOOSE_CLIENT');
		$uniqueClient[] = array("vendor_client" => JText::_('COM_TJVENDORS_FILTER_ALL_CLIENTS'),"client_value" => 0);

		foreach ($rows as $row)
		{
			$uniqueClient[] = array("vendor_client" => $row['vendor_client'], "client_value" => $row['vendor_client']);
		}

		return $uniqueClient;
	}

	/**
	 * Get array of unique Clients
	 * 
	 * @param   string  $vendor_id  integer
	 * 
	 * @param   string  $client     string
	 *  
	 * @return null|object
	 */
	public static function getTotalDetails($vendor_id,$client)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('*'));
		$query->from($db->quoteName('#__tjvendors_passbook'));
		$db->setQuery($query);

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . '=' . $vendor_id);
		}

		if (!empty($client))
		{
		$query->where($db->quoteName('client') . " = " . $db->quote($client));
		}

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

		$totalDetails = array("debitAmount" => $totalDebitAmount, "creditAmount" => $totalCreditAmount, "pendingAmount" => $totalpendingAmount);

		return $totalDetails;
	}

	/**
	 * Get array of unique Clients
	 * 
	 * @param   string  $vendor_id  integer
	 *  
	 * @return clientsForVendor 
	 */
	public static function getClientsForVendor($vendor_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('*'));
		$query->from($db->quoteName('#__vendor_client_xref'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $vendor_id);
		}

		$db->setQuery($query);

		if (!empty($rows = $db->loadAssocList()))
		{
			foreach ($rows as $client)
			{
				$clientsForVendor[] = $client['client'];
			}

			return $clientsForVendor;
		}
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
