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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

/**
 * View to show list of Affiliate logs
 *
 * @package  TJVendors
 *
 * @since    __DEPLOY_VERSION__
 */
class TjvendorsViewAffiliateLogs extends HtmlView
{
	/**
	 * Default log object
	 *
	 * @var  \JObject
	 */
	protected $items;

	/**
	 * Default component params
	 *
	 * @var  Joomla\Registry\Registry
	 */
	protected $params;
	/**
	 * Default pagination
	 *
	 * @var  JPAGINATION
	 */
	protected $pagination;

	/**
	 * Default active filters object
	 *
	 * @var  ARRAY
	 */
	protected $activeFilters;

	/**
	 * State
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * User object
	 *
	 * @var  Joomla\CMS\User\User
	 *
	 */
	protected $user;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$this->app  = Factory::getApplication();
		$this->user = Factory::getUser();
		$isroot = $this->user->authorise('core.admin');

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->params        = $this->app->getParams('com_tjcart');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (empty($this->user->id))
		{
			$msg = Text::_('COM_TJVENDOR_REGISTRATION_REDIRECT_MESSAGE');

			// Get current url.
			$current = Uri::getInstance()->toString();
			$url     = base64_encode($current);
			$this->app->redirect(Router::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
		}

		$vendorDetails = Table::getInstance('vendor', 'TjvendorsTable', array());
		$vendorDetails->load(array('user_id' => $this->user->id));

		if (!($vendorDetails->vendor_id) && !$isroot)
		{
			$this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->app->setHeader('status', 403, true);

			return;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function prepareDocument()
	{
		$menus = $this->app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_TJCART_PROMOTIONS_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $this->app->get('sitename');
		}
		elseif ($this->app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
		}
		elseif ($this->app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
