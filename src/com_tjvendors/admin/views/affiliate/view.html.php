<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsViewAffiliate extends HtmlView
{
	/**
	 * State data
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $state;

	/**
	 * Affiliate record
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $item;

	/**
	 * Affiliate form object
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $form;

	/**
	 * Affiliate form object
	 *
	 * @param   string  $tpl  Template layout
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since  __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$this->state 	= $this->get('State');
		$this->form   	= $this->get('Form');
		$app 	        = Factory::getApplication();
		$id             = $app->input->get('id', 0, '');
		$this->item	 	= $this->get('Item');

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjvendorsHelper::addSubmenu('affiliate');

		$this->addToolbar();

		if (!empty($client))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Method to add Tool bar
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since  __DEPLOY_VERSION__
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$viewTitle = Text::_('COM_TJVENDOR_AFFILIATE_ADD_NEW');
		}
		else
		{
			$viewTitle = Text::_('COM_TJVENDOR_AFFILIATE_EDIT');
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
		ToolbarHelper::title($viewTitle, 'pencil.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolbarHelper::apply('affiliate.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('affiliate.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			ToolbarHelper::custom('affiliate.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('affiliate.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('affiliate.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
