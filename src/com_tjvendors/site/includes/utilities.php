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
use Joomla\CMS\Table\Table;

/**
 * TJVendors utilities class for common methods.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsUtilities
{
	/**
	 * Constructor activating the default information of the utilities
	 *
	 * @since   __DEPLOY_VERSION__
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
	 * @return  countries
	 *
	 * @since   __DEPLOY_VERSION__
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
	 * @since   __DEPLOY_VERSION__
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
	 * @param   INT  $country_id  Country Id
	 *
	 * @return  countries
	 *
	 * @since   __DEPLOY_VERSION__
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
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCountry($countryId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$countryTable = Table::getInstance('Country', 'TjfieldsTable');
		$countryTable->load(array('id' => $countryId));

		$countryObj = new stdClass;
		$countryObj->id = $countryTable->id;
		$countryObj->country = $countryTable->country;

		return $countryObj;
	}
	
		/**
	 * Method to get state.
	 *
	 * @param   int  $regionId  region id
	 *
	 * @return object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getRegion($regionId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$regionTable = Table::getInstance('Region', 'TjfieldsTable');
		$regionTable->load(array('id' => $regionId));

		$regionObj = new stdClass;
		$regionObj->id = $regionTable->id;
		$regionObj->country_id = $regionTable->country_id;
		$regionObj->region = $regionTable->region;

		return $regionObj;
	}
	
		/**
	 * Method to get city.
	 *
	 * @param   int  $cityId  city id
	 *
	 * @return object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCity($cityId)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
		$cityTable = Table::getInstance('City', 'TjfieldsTable');
		$cityTable->load(array('id' => $cityId));

		$cityObj = new stdClass;
		$cityObj->id = $cityTable->id;
		$cityObj->city = $cityTable->city;
		$cityObj->country_id = $cityTable->country_id;

		return $cityObj;
	}
}
