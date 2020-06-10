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
}
