<?php
/**
 * @version    SVN: 
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Vendor controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendorFee extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
			$this->view_list = 'vendorfees';
		$this->input = JFactory::getApplication()->input;

		if (empty($this->client))
		{
			$this->client = $this->input->get('client', '');
		}

		parent::__construct();
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key fee_id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the fee_id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$input = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$currency = $formData->get('currency');
		$vendor_id = (int) $formData->get('vendor_id');
		$feeId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$client = (STRING) (count($cid) ? $cid[0] : $input->get('client'));
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&client=' . $client . '&vendor_id=' . $vendor_id . '&currency=' . $currency;

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&client=' . $this->client;

		return $append;
	}

	/**
	 * Function to cancel button redirection
	 * 
	 * @param   integer  $key  The primary key fee_id for the item.
	 * 
	 * @return  void
	 */
	public function cancel($key = null)
	{
		$input = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$vendorId = (int) $formData->get('vendor_id');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendor = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		$vendorDetail = $TjvendorsModelVendor->getItem();
		$VendorCurrency = $vendorDetail->currency;
		$currencies = json_decode($VendorCurrency);
		$client = $this->input->get('client', '', 'STRING');

		foreach ($currencies as $currency)
		{
		$curr .= "&currency[]=" . $currency;
		}

		$link = JRoute::_('index.php?option=com_tjvendors&view=vendorfees&client=' . $client . '&vendor_id=' . $vendorId . $curr, false);
		$this->setRedirect($link);
	}

	/**
	 * Function to edit field data
	 *
	 * @param   integer  $key  The primary key fee_id for the item.
	 * 
	 * @return  void
	 */
	public function edit($key = null)
	{
		$input    = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$vendorId = (int) (count($cid) ? $cid[0] : $input->getInt('vendor_id'));
		$currency = (STRING) (count($cid) ? $cid[0] : $input->get('currency'));
		$feeId = (int) (count($cid) ? $cid[0] : $input->getInt('fee_id'));
		$currencies = json_decode($VendorCurrency);
		$client = $this->input->get('client', '', 'STRING');
		$link = JRoute::_(
		'index.php?option=com_tjvendors&view=vendorfee&layout=edit&client=' . $client . '&id=' . $feeId . '&vendor_id=' . $vendorId .
		'&currency=' . $currency, false
		);
		$this->setRedirect($link);
	}
}
