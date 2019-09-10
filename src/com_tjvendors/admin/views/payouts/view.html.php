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

jimport('joomla.application.component.view');
JLoader::import('com_tjvendors.helpers.fronthelper', JPATH_SITE . '/components');

/**
 * View class for a list of Tjvendors.
 *
 * @since  1.6
 */
class TjvendorsViewPayouts extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $vendor_details;

	protected $uniqueClients;

	protected $bulkPayoutStatus;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->model = $this->getModel('payouts');
		$this->input = JFactory::getApplication()->input;

		// Getting vendor id from url
		$vendor_id = $this->input->get('vendor_id', '', 'INT');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendors');
		$tjvendorsModelVendors = JModelLegacy::getInstance('Vendors', 'TjvendorsModel');
		$vendorsDetail = $tjvendorsModelVendors->getItems();
		$this->vendor_details = $vendorsDetail;
		$this->uniqueClients = TjvendorsHelper::getUniqueClients();
		$com_params = JComponentHelper::getParams('com_tjvendors');
		$this->bulkPayoutStatus = $com_params->get('bulk_payout');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjvendorsHelper::addSubmenu('payouts');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$input = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');

		$state = $this->get('State');
		$canDo = TjvendorsHelper::getActions();
		JToolBarHelper::custom('back', 'chevron-left.png', '', 'COM_TJVENDORS_BACK', false);

		$tjvendorFrontHelper = new TjvendorFrontHelper;
		$clientTitle = $tjvendorFrontHelper->getClientName($client);

		$title = !empty($client) ? $clientTitle . ' : ' : '';

		JToolbarHelper::title($title . JText::_('COM_TJVENDORS_TITLE_PAYOUTS'), 'list.png');

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_tjvendors');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_tjvendors&view=payouts');

		$this->extra_sidebar = '';
	}
}
