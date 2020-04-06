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
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * View to edit
 *
 * @since  1.6
 */
class TjvendorsViewVendor extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $vendor_id;

	protected $VendorDetail;

	protected $vendorClientXrefTable;

	protected $layout;

	protected $vendor;

	protected $input;

	protected $client;

	protected $params;

	protected $isClientExist;

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
		$this->params = ComponentHelper::getParams('com_tjvendors');
		$this->state  = $this->get('State');
		$this->vendor = $this->get('Item');
		$this->form   = $this->get('Form');
		$this->input  = Factory::getApplication()->input;
		$this->client = $this->input->get('client', '', 'STRING');

		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjvendors/models', 'vendor');
		$tjvendorsModelVendor        = BaseDatabaseModel::getInstance('Vendor', 'TjvendorsModel');
		$tjvendorFrontHelper         = new TjvendorFrontHelper;
		$this->vendor_id             = $tjvendorFrontHelper->getvendor();
		$this->client                = $this->input->get('client', '', 'STRING');
		$this->isClientExist         = $tjvendorFrontHelper->isClientExist($this->client, $this->vendor_id);
		$this->vendorClientXrefTable = Table::getInstance('vendorclientxref', 'TjvendorsTable', array());
		$this->vendorClientXrefTable->load(array('vendor_id' => $this->vendor_id, 'client' => $this->client));
		$this->VendorDetail          = $tjvendorsModelVendor->getItem($this->vendor_id);

		$utilitiesObj = TJVendors::utilities();
		$this->countries = $utilitiesObj->getCountries();

		$this->default = null;

		if (isset($this->vendor->country))
		{
			$this->default = $this->vendor->country;
		}

		$this->options = array();
		$this->options[] = HTMLHelper::_('select.option', 0, Text::_('COM_TJVENDORS_FORM_LIST_SELECT_OPTION'));

		foreach ($this->countries as $key => $value)
		{
			$country = $this->countries[$key];
			$id      = $country['id'];
			$value   = $country['country'];
			$this->options[] = HTMLHelper::_('select.option', $id, $value);
		}

		$app = Factory::getApplication();
		$app->setUserState("vendor.client", $this->client);
		$app->setUserState("vendor.vendor_id", $this->vendor->vendor_id);
		$this->layout = $this->input->get('layout', '', 'STRING');
		Text::script('COM_TJVENDOR_PAYMENTGATEWAY_NO_FIELD_MESSAGE');
		Text::script('COM_TJVENDOR_DESCRIPTION_READ_MORE');
		Text::script('COM_TJVENDOR_DESCRIPTION_READ_LESS');
		
		if ($this->layout == 'profile' && $this->vendor_id != $this->vendor->vendor_id)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if (!empty($this->vendor_id) && $this->layout == "edit")
		{
			if (!empty($this->clientsForVendor))
			{
				foreach ($this->clientsForVendor as $client)
				{
					if ($client == $this->client)
					{
						$link = Route::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $this->client . '&vendor_id=' . $this->vendor_id);
						$app = Factory::getApplication();
						$app->enqueueMessage(Text::_('COM_TJVENDOR_REGISTRATION_REDIRECT_MESSAGE'));
						$app->redirect($link);
					}
				}
			}
		}

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
