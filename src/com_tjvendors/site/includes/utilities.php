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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

/**
 * TJVendors utilities class for common methods.
 *
 * @since  1.4.0
 */
class TjvendorsUtilities
{
	public $tjGeoHelperObj;

	/**
	 * Constructor activating the default information of the utilities
	 *
	 * @since   1.4.0
	 */
	public function __construct()
	{
		$TjGeoHelper = JPATH_ROOT . '/components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$this->tjGeoHelperObj = new TjGeoHelper;
	}

	/**
	 * Methods to get countries
	 *
	 * @return  Array  country
	 *
	 * @since   1.4.0
	 */
	public function getCountries()
	{
		$rows = $this->tjGeoHelperObj->getCountryList('com_tjvendors');

		return $rows;
	}

	/**
	 * Methods to get regions
	 *
	 * @param   Int  $countryId  country id
	 * 
	 * @return  Array  regions
	 *
	 * @since   1.4.0
	 */
	public function getRegions($countryId)
	{
		if (!$countryId)
		{
			return;
		}

		$rows = $this->tjGeoHelperObj->getRegionList($countryId, 'com_tjvendors');

		return $rows;
	}

	/**
	 * This methods returns the cities for given country
	 *
	 * @param   INT  $countryId  Country Id
	 *
	 * @return  array   city list
	 *
	 * @since   1.4.0
	 */
	public function getCities($countryId)
	{
		if (!$countryId)
		{
			return;
		}

		$rows = $this->tjGeoHelperObj->getCityList($countryId, 'com_tjvendors');

		return $rows;
	}

	/**
	 * Method to get country.
	 *
	 * @param   int  $countryId  country id
	 *
	 * @return object
	 *
	 * @since   1.4.0
	 */
	public function getCountry($countryId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$countryTable = Table::getInstance('Country', 'TjfieldsTable');
		$countryTable->load(array('id' => $countryId));

		$countryObj = new stdClass;

		if ($countryTable)
		{
			$countryObj->id      = $countryTable->id;
			$countryObj->country = (property_exists($countryTable, 'country') ? $countryTable->country : '');
		}

		return $countryObj;
	}

	/**
	 * Method to get state.
	 *
	 * @param   int  $regionId  region id
	 *
	 * @return object
	 *
	 * @since   1.4.0
	 */
	public function getRegion($regionId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$regionTable = Table::getInstance('Region', 'TjfieldsTable');
		$regionTable->load(array('id' => $regionId));

		$regionObj = new stdClass;

		if ($regionTable)
		{
			$regionObj->id         = $regionTable->id;
			$regionObj->country_id = (property_exists($regionTable, 'country_id') ? $regionTable->country_id : '');
			$regionObj->region     = (property_exists($regionTable, 'region') ? $regionTable->region : '');
		}

		return $regionObj;
	}

	/**
	 * Method to get city.
	 *
	 * @param   int  $cityId  city id
	 *
	 * @return object
	 *
	 * @since   1.4.0
	 */
	public function getCity($cityId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$cityTable = Table::getInstance('City', 'TjfieldsTable');
		$cityTable->load(array('id' => $cityId));

		$cityObj = new stdClass;

		if ($cityTable)
		{
			$cityObj->id         = $cityTable->id;
			$cityObj->city       = (property_exists($cityTable, 'city') ? $cityTable->city : '');
			$cityObj->country_id = (property_exists($cityTable, 'country_id') ? $cityTable->country_id : '');
		}

		return $cityObj;
	}

	/**
	 * Set the language constant used in the javascript operation
	 *
	 * @return   void
	 *
	 * @since   1.4.0
	 */
	public function defineLanguageConstant()
	{
		Text::script('COM_TJVENDOR_VENDOR_FORM_AJAX_FAIL_ERROR_MESSAGE');
	}

	/**
	 * This function returns the vendor country list.
	 * For e.g in JGive there is 3 vendor belongs from 3 different countries,
	 * for filtering campaigns by country it returning the array of those 3 countries
	 *
	 * @param   string  $extension  extension name like com_jgive
	 * 
	 * @return   Array 
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getFilterCountries($extension)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('con.id as country_id');
		$query->select('con.country');
		$query->from($db->qn('#__tj_country', 'con'));
		$query->join('INNER', $db->qn('#__tjvendors_vendors', 'vendors') . ' ON (' . $db->qn('vendors.country') . ' = ' . $db->qn('con.id') . ')');
		$query->join('LEFT', $db->qn('#__vendor_client_xref', 'vedorxref') .
		'ON (' . $db->qn('vendors.vendor_id') . ' = ' . $db->qn('vedorxref.vendor_id') . ')');
		$query->where($db->qn('vendors.state') . ' = 1 AND ' . $db->qn('vedorxref.client') . ' = ' . $db->quote($extension));
		$query->group($db->qn('vendors.country'));
		$db->setQuery($query);

		return $db->loadobjectlist();
	}

	/**
	 * Function getFilterRegion fetch the region base on country id and extension
	 *
	 * @param   int     $country    country id
	 * @param   string  $extension  extension name like com_jgive
	 * 
	 * @return  Array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getFilterRegion($country, $extension)
	{
		if (!empty($country))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('r.id'));
			$query->select($db->qn('r.region'));
			$query->from($db->qn('#__tj_region', 'r'));
			$query->join('LEFT', $db->qn('#__tjvendors_vendors', 'vendors') . ' ON (' . $db->qn('r.id') . ' = ' . $db->qn('vendors.region') . ')');
			$query->join('LEFT', $db->qn('#__vendor_client_xref', 'vedorxref') .
			'ON (' . $db->qn('vendors.vendor_id') . ' = ' . $db->qn('vedorxref.vendor_id') . ')');
			$query->where($db->qn('vendors.state') . ' = 1 AND ' . $db->qn('vedorxref.client') . ' = ' . $db->quote($extension));
			$query->where($db->qn('vendors.country') . ' = ' . (int) $country);
			$query->group($db->qn('vendors.region'));
			$db->setQuery($query);

			return $db->loadobjectlist();
		}
	}

	/**
	 * Function getFilterCity fetch the city base on region id and extension
	 *
	 * @param   int     $region     region id
	 * @param   string  $extension  extension name like com_jgive
	 * 
	 * @return  Array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getFilterCity($region, $extension)
	{
		if (!empty($region))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('city.id'));
			$query->select($db->qn('city.city'));
			$query->from($db->qn('#__tj_city', 'city'));
			$query->join('RIGHT', $db->qn('#__tjvendors_vendors', 'vendors') . ' ON (' . $db->qn('city.id') . ' = ' . $db->qn('vendors.city') . ')');
			$query->join('LEFT', $db->qn('#__vendor_client_xref', 'vedorxref') .
			'ON (' . $db->qn('vendors.vendor_id') . ' = ' . $db->qn('vedorxref.vendor_id') . ')');
			$query->where($db->qn('vendors.state') . ' = 1 AND ' . $db->qn('vedorxref.client') . ' = ' . $db->quote($extension));
			$query->where($db->qn('vendors.region') . ' = ' . (int) $region);
			$query->group($db->qn('vendors.city'));
			$db->setQuery($query);

			return $db->loadobjectlist();
		}
	}
}
