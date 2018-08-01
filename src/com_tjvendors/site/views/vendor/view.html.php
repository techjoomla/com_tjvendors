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

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class TjvendorsViewVendor extends JViewLegacy
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
		$this->params = JComponentHelper::getParams('com_tjvendors');
		$this->state  = $this->get('State');
		$this->vendor = $this->get('Item');
		$this->form   = $this->get('Form');
		$this->input  = JFactory::getApplication()->input;
		$this->client = $this->input->get('client', '', 'STRING');

		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjvendors/models', 'vendor');
		$tjvendorsModelVendor        = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		$tjvendorFrontHelper         = new TjvendorFrontHelper;
		$this->vendor_id             = $tjvendorFrontHelper->getvendor();
		$this->vendorClientXrefTable = JTable::getInstance('vendorclientxref', 'TjvendorsTable', array());
		$this->vendorClientXrefTable->load(array('vendor_id' => $this->vendor_id, 'client' => $this->client));
		$this->VendorDetail          = $tjvendorsModelVendor->getItem($this->vendor_id);

		$app = JFactory::getApplication();
		$app->setUserState("vendor.client", $this->client);
		$app->setUserState("vendor.vendor_id", $this->vendor->vendor_id);
		$this->layout = $this->input->get('layout', '', 'STRING');
		JText::script('COM_TJVENDOR_PAYMENTGATEWAY_NO_FIELD_MESSAGE');
		JText::script('COM_TJVENDOR_DESCRIPTION_READ_MORE');
		JText::script('COM_TJVENDOR_DESCRIPTION_READ_LESS');

		if ($this->layout == 'profile' && $this->vendor_id != $this->vendor->vendor_id)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if (!empty($this->vendor_id) && $this->layout == "edit")
		{
			if (!empty($this->clientsForVendor))
			{
				foreach ($this->clientsForVendor as $client)
				{
					if ($client == $this->client)
					{
						$link = JRoute::_('index.php?option=com_tjvendors&view=vendor&layout=profile&client=' . $this->client . '&vendor_id=' . $this->vendor_id);
						$app = JFactory::getApplication();
						$app->enqueueMessage(JText::_('COM_TJVENDOR_REGISTRATION_REDIRECT_MESSAGE'));
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
