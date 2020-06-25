<?php
/**
 * @package     TJVendor
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;

/**
 * Vendor Json controller class
 *
 * @since  1.4.0
 */
class TjvendorsControllerVendor extends TjvendorsController
{
	/**
	 * This method loads regions according to selected country
	 * called via jquery ajax
	 *
	 * @return  void
	 */
	public function getRegion()
	{
		$app           = Factory::getApplication();
		$input         = $app->input;
		$country       = $input->get('country', 0, 'INT');
		$defaultRegion = array(
			"id"           => '',
			"region"       => Text::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION'),
			"region_jtext" => Text::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION')
		);

		$utilitiesObj  = TJVendors::utilities();
		$regions       = $utilitiesObj->getRegions($country);

		if (!empty($regions))
		{
			array_unshift($regions, $defaultRegion);
		}
		else
		{
			$regions[] = $defaultRegion;
		}

		echo new JResponseJson($regions);
		$app->close();
	}

	/**
	 * Loads city according to selected country
	 * called via jquery ajax
	 *
	 * @return  void
	 */
	public function getCity()
	{
		$app         = Factory::getApplication();
		$input       = $app->input;
		$country     = $input->get('country', 0, 'INT');
		$defaultCity = array(
			"id"         => '',
			"city"       => Text::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION'),
			"city_jtext" => Text::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION')
		);

		// Use helper file function
		$utilitiesObj  = TJVendors::utilities();
		$city          = $utilitiesObj->getCities($country);

		if (!empty($city))
		{
			array_unshift($city, $defaultCity);
			$otherCity = array("id" => 'other', "city" => Text::_('COM_TJVENDORS_VENDOR_OTHER_CITY_OPTION'), "city_jtext" => 'other');
			array_push($city, $otherCity);
		}
		else
		{
			$city[] = $defaultCity;
		}

		echo new JResponseJson($city);
		$app->close();
	}

	/**
	 * This method auto saving vendor details. 
	 * This method will call only after user has selected Stripe payment gateway in vendor form.
	 *
	 * @return  String  Json Response
	 */
	public function autoVendorSave()
	{
		$app                 = Factory::getApplication();
		$plugin              = PluginHelper::getPlugin('payment', 'stripe');
		$pluginParams        = new Registry($plugin->params);
		$enableStripeConnect = $pluginParams->get('enableconnect');

		// Do not allowing for saving vendor details if "Enabled stripe connect" is off
		if ($enableStripeConnect == '0')
		{
			echo new JsonResponse('', Text::_('COM_TJVENDORS_VENDOR_ERROR_MSG_USING_STRIPE_SAVE'), true);
			$app->close();
		}

		$input         = $app->input->post;
		$post          = $input->get('data', array(), 'Array');
		$vendorData    = json_decode($post[0]);
		$formattedData = array();

		/* Array comes like below. To get exactly required key value pair data use below foreach
		Array(
			[0] => stdClass Object
			(
				[name] => jform[vendor_id]
				[value] => 1
			)
		)
		 */
		foreach ($vendorData as $key => $data)
		{
			$indexTitle = $data->name;

			if (strpos($indexTitle, 'jform') !== false)
			{
				$indexTitle = substr($data->name, 6);
				$indexTitle = substr($indexTitle, 0, -1);
			}

			$formattedData[$indexTitle] = $data->value;
		}

		if (empty($formattedData['vendor_title']))
		{
			echo new JsonResponse('', Text::_('COM_TJVENDORS_VENDORS_VENDOR_TITLE'), true);
			$app->close();
		}

		$formattedData['vendor_client'] = $app->input->get('client', '', 'String') ? $app->input->get('client', '', 'String') : $formattedData['client'];
		$formattedData['user_id'] = (isset($formattedData['user_id'])) ? $formattedData['user_id']: $formattedData['created_by'];
		$userId = $formattedData['user_id'];
		$formattedData['approved'] = 1;

		$paymentData = array();

		foreach ($formattedData as $key => $value)
		{
			if (strpos($key, 'payment_gateway') !== false)
			{
				$paymentData[$key] = $value;
				unset($formattedData[$key]);
			}
		}

		$formattedData['payment_gateway'] = array();
		$i = 0;

		/* To get Payment gateway information and add in main array
		[payment_gateway] => Array
        (
            [payment_gateway0] => Array
                (
                    [payment_gateways] => paypal
                    [payment_email_id] => riya@erwer.com
                )

        )*/
		foreach ($paymentData as $key => $value)
		{
			if (strpos($key, 'payment_gateway' . $i) !== false && strpos($key, 'payment_gateways') !== false)
			{
				$formattedData['payment_gateway']['payment_gateway' . $i] = array();
				$i++;
			}
		}

		foreach ($formattedData['payment_gateway'] as $key => $sub)
		{
			foreach ($paymentData as $index => $value)
			{
				if (strpos($index, $key) !== false && strpos($index, 'payment_gateways') !== false)
				{
					$formattedData['payment_gateway'][$key]['payment_gateways'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'payment_email_id') !== false)
				{
					$formattedData['payment_gateway'][$key]['payment_email_id'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeLive') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeLive'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorEmail') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorEmail'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeUserId') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeUserId'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeTokenLive') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeTokenLive'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeKeyLive') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeKeyLive'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeTokenTest') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeTokenTest'] = $value;
				}

				if (strpos($index, $key) !== false && strpos($index, 'vendorStripeKeyTest') !== false)
				{
					$formattedData['payment_gateway'][$key]['vendorStripeKeyTest'] = $value;
				}
			}
		}

		$model = $this->getModel('Vendor', 'TjvendorsModel');
		$return = $model->save($formattedData);

		if ($return)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('vendor_id'));
			$query->from($db->quoteName('#__tjvendors_vendors'));
			$query->where($db->quoteName('user_id') . ' = ' . (int) $userId);
			$db->setQuery($query);
			$vendor_id = $db->loadResult();
			$message = (!empty($vendor_id)) ? Text::_('COM_TJVENDORS_MSG_SUCCESS_SAVE_VENDOR') : Text::_('COM_TJVENDORS_VENDOR_ERROR_MSG_SAVE');

			echo new JsonResponse(array('vendor_id' => $vendor_id), $message);
			$app->close();
		}

		echo new JsonResponse(null, Text::_('Invalid Data'), true);
		$app->close();
	}
}
