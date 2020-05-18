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
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Vendor controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerPayout extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'payouts';
		$this->input = JFactory::getApplication()->input;

		if (empty($this->client))
		{
			$this->client = $this->input->get('client', '');
		}

		parent::__construct();
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
		$input = JFactory::getApplication()->input;
		$vendor_id = $input->get('vendor_id', '', 'INTEGER');
		$client = $input->get('client', '', 'STRING');
		$append = parent::getRedirectToListAppend();
		$append .= '&vendor_id=' . $vendor_id;
		$append .= '&client=' . $client;

		return $append;
	}

	/**
	 * Add credit entry for the purchase.
	 *
	 * @return  null
	 *
	 * @since   1.6
	 */
	public function addCreditEntry()
	{
		$model = $this->getModel('payout');
		$results = $model->addCreditEntry();
	}

	/**
	 * Change payout status.
	 *
	 * @return  null
	 *
	 * @since   1.6
	 */
	public function changePayoutStatus()
	{
		$input  = JFactory::getApplication()->input->post;
		$payout_id = $input->get('payout_id', '', 'STRING');
		$paidUnpaid = $input->get('paidUnpaid', '', 'STRING');
		$model = $this->getModel('Payout');
		$results = $model->changePayoutStatus($payout_id, $paidUnpaid);

		echo json_encode($results);
		jexit();
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		$app = Factory::getApplication();
		$vendor_id =$app->input->get('vendor_id', '', 'INTEGER');
		$client = $app->input->get('client', '', 'STRING');
		$data = $app->input->get('jform', array(), 'array');
		$model  = $this->getModel('payout', 'TjvendorsModel');
		$return = $model->save($data);
		$this->setRedirect(Route::_('index.php?option=com_tjvendors&view=payouts&vendor_id' . $vendor_id . '&client=' . $client, false));

		if($return != 1)
		{
			return $app->enqueueMessage(Text::_('COM_TJVENDORS_PAYOUT_UNSUCCESSFULL_MESSAGE'));;
		}

		return $app->enqueueMessage(Text::_('COM_TJVENDORS_PAYOUT_SUCCESSFULL_MESSAGE'));;
	}
}
