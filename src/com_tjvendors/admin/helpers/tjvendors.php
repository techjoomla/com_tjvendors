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
class TjvendorsHelper
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

					$lang->load($component, JPATH_BASE, null, false, false)
					|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), null, false, false)
					|| $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
					|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), $lang->getDefault(), false, false);

					call_user_func(array($cName, 'addSubmenu'), $vName . (isset($section) ? '.' . $section : ''));
				}
			}
		}

		$currentComponent = $input->get('extension', '', 'STRING');

		if ($currentComponent == 'com_tjvendors')
		{
			$notifications  = '';

			$app = JFactory::getApplication();
			$queue        = $app->input->get('layout');
			$option = $app->input->get('option');

			switch ($vName)
			{
				case 'notifications':
					$notifications = true;
					break;
			}

			JHtmlSidebar::addEntry(
				JText::_('COM_TJVENDORS_TJNOTIFICATIONS_MENU'), 'index.php?option=com_tjnotifications&extension=com_tjvendors',
				$notifications
			);

			// Load bootsraped filter

			JHtml::_('bootstrap.tooltip');
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
		$columns = $db->quoteName('client');
		$query->select('distinct' . $columns);
		$query->from($db->quoteName('#__vendor_client_xref'));
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		$uniqueClient[] = array("vendor_client" => JText::_('JFILTER_PAYOUT_CHOOSE_CLIENT'), "client_value" => '');

		foreach ($rows as $row)
		{
			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$langClient = $tjvendorFrontHelper->getClientName($row['client']);
			$uniqueClient[] = array("vendor_client" => $langClient, "client_value" => $row['client']);
		}

		return $uniqueClient;
	}

	/**
	 * Get total amount
	 *
	 * @param   integer  $vendor_id  integer
	 *
	 * @param   string   $currency   integer
	 *
	 * @param   string   $client     integer
	 *
	 * @return client|array
	 */
	public static function getTotalAmount($vendor_id, $currency, $client)
	{
		$com_params = JComponentHelper::getParams('com_tjvendors');
		$bulkPayoutStatus = $com_params->get('bulk_payout');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$subQuery = $db->getQuery(true);
		$subQuery->select('max(' . $db->quoteName('id') . ')');
		$subQuery->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($vendor_id))
		{
			$subQuery->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		if (!empty($currency))
		{
			$subQuery->where($db->quoteName('currency') . ' = ' . $db->quote($currency));
		}

		if (!empty($client))
		{
			$subQuery->where($db->quoteName('client') . ' = ' . $db->quote($client));
		}

		$query->select('*');
		$query->from($db->quoteName('#__tjvendors_passbook'));
		$query->where($db->quoteName('id') . ' = (' . $subQuery . ')');

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	/**
	 * Get bulk pending amount
	 *
	 * @param   integer  $vendor_id  integer
	 *
	 * @param   string   $currency   integer
	 *
	 * @return $bulkPendingAmount
	 */
	public static function bulkPendingAmount($vendor_id, $currency)
	{
		$vendorClients = self::getClients($vendor_id);
		$bulkPendingAmount = 0;

		foreach ($vendorClients as $client)
		{
			$pendingAmount = self::getPayableAmount($vendor_id, $client['client'], $currency);
			$bulkPendingAmount = $bulkPendingAmount + $pendingAmount;
		}

		return $bulkPendingAmount;
	}

	/**
	 * Get array of clients
	 *
	 * @param   string  $vendor_id  integer
	 *
	 * @return client|array
	 */
	public static function getClients($vendor_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('distinct' . $db->quoteName('client'));
		$query->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		$db->setQuery($query);
		$clients = $db->loadAssocList();

		return $clients;
	}

	/**
	 * Get get unique Currency
	 *
	 * @param   string  $currency   integer
	 *
	 * @param   string  $vendor_id  integer
	 *
	 * @param   string  $client     integer
	 *
	 * @return boolean
	 */

	public static function checkUniqueCurrency($currency, $vendor_id, $client)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('currency'));
		$query->from($db->quoteName('#__tjvendors_fee'));

		if (!empty($client))
		{
		$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		if (!empty($client))
		{
			$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		}

		$db->setQuery($query);
		$currencies = $db->loadAssocList();

		foreach ($currencies as $i)
		{
			if ($currency == $i['currency'])
			{
				return false;
				break;
			}
			else
			{
				continue;
			}
		}

		return true;
	}

	/**
	 * Get get currencies
	 *
	 * @param   string  $vendor_id  integer
	 *
	 * @return currencies|array
	 */
	public static function getCurrencies($vendor_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT' . $db->quoteName('currency'));
		$query->from($db->quoteName('#__tjvendors_passbook'));

		if (!empty($vendor_id))
		{
			$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		}

		$db->setQuery($query);
		$currencies = $db->loadAssocList();

		return $currencies;
	}

	/**
	 * Get get vendor_id
	 *
	 * @param   integer  $vendor_id  integer
	 *
	 * @param   string   $client     string
	 *
	 * @param   string   $currency   string
	 *
	 * @return res|integer
	 */
	public static function getPayableAmount($vendor_id, $client, $currency)
	{
		$com_params = JComponentHelper::getParams('com_tjvendors');
		$payout_day_limit = $com_params->get('payout_limit_days', '0', 'INT');
		$date = JFactory::getDate();
		$payout_date_limit = $date->modify("-" . $payout_day_limit . " day");
		$bulkPayoutStatus = $com_params->get('bulk_payout');

		// Query to get the credit amount
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('SUM(' . $db->quoteName('CREDIT') . ')');
		$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		$query->where($db->quoteName('currency') . ' = ' . $db->quote($currency));
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('transaction_time') . ' < ' . $db->quote($payout_date_limit));
		$query->from($db->quoteName('#__tjvendors_passbook'));
		$db->setQuery($query);
		$credit = $db->loadResult();

		// Query to get debit data
		$query = $db->getQuery(true);
		$query->select('SUM(' . $db->quoteName('debit') . ')');
		$query->from($db->quoteName('#__tjvendors_passbook'));
		$query->where($db->quoteName('vendor_id') . ' = ' . $db->quote($vendor_id));
		$query->where($db->quoteName('currency') . ' = ' . $db->quote($currency));
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('status') . ' = ' . $db->quote(1));
		$db->setQuery($query);
		$debit = $db->loadResult();
		$payableAmount = $credit - $debit;

		return $payableAmount;
	}

	/**
	 * Get get currencies
	 *
	 * @param   string  $data  integer
	 *
	 * @return currencies|array
	 */
	public static function addVendor($data)
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$tjvendorsModelVendors = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables', 'vendor');
		$vendorsDetail = $tjvendorsModelVendors->save($data);
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjvendors/tables');
		$db = JFactory::getDbo();
		$table = JTable::getInstance('vendor', 'TJVendorsTable', array());
		$table->load(array('user_id' => $data['user_id']));

		return $table->vendor_id;
	}
}
