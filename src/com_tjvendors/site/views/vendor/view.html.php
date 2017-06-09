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
		$this->user_id = jFactory::getuser()->id;
		$this->vendor  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->input = JFactory::getApplication()->input;
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendor = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		$this->vendor_id = TjvendorsHelpersTjvendors::getvendor();
		$this->VendorDetail = $TjvendorsModelVendor->getItem($this->vendor_id);
		$this->clientsForVendor = TjvendorsHelpersTjvendors::getClientsForVendor($this->vendor_id);
		$this->client = $this->input->get('client', '', 'STRING');
		$app = JFactory::getApplication();
		$app->setUserState("vendor.client", $this->client);
		$app->setUserState("vendor.vendor_id", $this->vendor->vendor_id);

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
