<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

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
		$app = Factory::getApplication();
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->params = ComponentHelper::getParams('com_tjvendors');
		$this->input = Factory::getApplication()->input;
		Text::script('COM_TJVENDOR_DUPLICARE_VENDOR_ERROR');
		Text::script('COM_TJVENDOR_PAYMENTGATEWAY_NO_FIELD_MESSAGE');
		Text::script('COM_TJVENDOR_USER_ERROR');
		$this->client = $this->input->get('client', '', 'STRING');

		if (empty($this->item->vendor_id))
		{
			$currUrl = $this->input->get('currency', '', 'ARRAY');
			$this->item->currency = json_encode($currUrl);
			$this->item->vendor_client = $this->client;
		}

		$app->setUserState("vendor.client", $this->client);
		$app->setUserState("vendor.vendor_id", $this->item->vendor_id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->vendor_id == 0);

		$input = Factory::getApplication()->input;
		$this->full_client = $input->get('client', '', 'STRING');

		if ($isNew)
		{
			$viewTitle = Text::_('COM_TJVENDOR_VENDORS_ADD_USER_SPECIFIC_COMM');
		}
		else
		{
			$viewTitle = Text::_('COM_TJVENDOR_VENDORS_EDIT_USER_SPECIFIC_COMM');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = TjvendorsHelper::getActions();
		$clientTitle = TjvendorFrontHelper::getClientName($this->client);
		ToolbarHelper::title($clientTitle . '  ' . $viewTitle, 'pencil.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('vendor.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('vendor.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('vendor.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if (empty($this->item->vendor_id))
		{
			JToolBarHelper::cancel('vendor.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('vendor.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
