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
class TjvendorsViewAffliates extends JViewLegacy
{
	public $state;

	public $items;

	public $pagination;

	public $sidebar;

	public $filterForm;

	public $activeFilters;

	/**
	 * Function to display.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths
	 *
	 * @return  boolean
	 *
	 * @since   2.3.0
	 */
	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$user      = JFactory::getUser();

		if (!JFactory::getUser($user->id)->authorise('core.manage', 'com_tjvendors'))
		{
			$mainframe->enqueueMessage(JText::_('COM_TJVENDORS_AUTH_ERROR'), 'error');

			return false;
		}

		// Load submenu
		TjvendorsHelper::addSubmenu('affliates');

		// Get state
		$this->state = $this->get('State');

		// Get data from the model
		$this->items = $this->get('Items');

		$this->pagination = $this->get('Pagination');

		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		// Get the toolbar object instance
		// JToolbarHelper::title(JText::_("COM_JGIVE") . ": " . JText::_('COM_JGIVE_INDIVIDUALS'), 'list');
		// JToolBarHelper::preferences('com_jgive');

		// Set the toolbar
		// $this->addToolBar();

		$this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   2.3.0
	 */
/*	protected function addToolBar()
	{
		$user       = JFactory::getUser();
		$canCreate  = $user->authorise('core.create', 'com_tjvendors');
		$canEdit    = $user->authorise('core.edit', 'com_tjvendors');
		$canDelete  = $user->authorise('core.delete', 'com_tjvendors');

		if ($canCreate)
		{
			// Add buttons on toolbar
			//JToolBarHelper::addNew('individual.add');
		}

		if ($canEdit && !empty($this->items))
		{
			//JToolBarHelper::editList('individual.edit');
		}

		if ($canDelete && !empty($this->items))
		{
			// JToolBarHelper::deleteList('', 'affliates.delete');
			// JToolBarHelper::divider();
		}
	}*/
}
