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

/**
 * TJVendors utilities class for common methods.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsUtilities
{
	/**
	 * Methods to get countries
	 *
	 * @return  countries
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCountries()
	{
		$TjGeoHelper = JPATH_ROOT . '/components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$tjGeoHelperObj = new TjGeoHelper;
		$rows = $tjGeoHelperObj->getCountryList('com_tjvendors');

		return $rows;
	}
}
