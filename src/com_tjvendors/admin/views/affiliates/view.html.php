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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of Affiliates.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsViewAffiliates extends HtmlView
{
	/**
	 * List of records
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $items;

	/**
	 * Pagination data
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pagination;

	/**
	 * State data
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $state;

	/**
	 * Filter Form data
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	public $filterForm;

	/**
	 * Active Filters data
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	public $activeFilters;

	/**
	 * An ACL object to verify user rights.
	 *
	 * @var    Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $canDo;

	/**
	 * Affiliates List to display
	 *
	 * @param   string  $tpl  Template layout
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since  __DEPLOY_VERSION__
	 */

	public function display($tpl = null)
	{
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->canDo         = TjvendorsHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjvendorsHelper::addSubmenu('affiliates');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = TjvendorsHelper::getActions();
		ToolbarHelper::addNew('affiliate.add');

		ToolbarHelper::title(Text::_('COM_TJVENDORS_TITLE_AFFILIATES'), 'list.png');

		if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			ToolbarHelper::editList('affiliate.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				ToolbarHelper::divider();
				ToolbarHelper::custom('affiliates.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolbarHelper::custom('affiliates.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}

			if (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				ToolbarHelper::deleteList('', 'affiliates.delete', 'JTOOLBAR_DELETE');
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::preferences('com_tjvendors');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_tjvendors&view=affiliates');
	}
}
