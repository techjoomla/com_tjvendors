<?php
/**
 * @package     TJVendors
 * @subpackage  Privacy.tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * TJVendors Plugin.
 *
 * @since  2.3.4
 */
class PlgTjvendorsTjvendors extends JPlugin
{
	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * On saving vendor data
	 *
	 * Method is called after vendor data is stored in the database.
	 *
	 * @param   array    $data   Holds the new vendor data.
	 * @param   boolean  $isNew  True if a new vendor is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function TjVendorOnAfterVendorSave($data, $isNew)
	{
	}

	/**
	 * On after payout is added
	 *
	 * Method is called after payout is added.
	 *
	 * @param   array  $data  Holds the event data.
	 * @param   boolean  $isNew  True if payout is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function TjVendorOnAfterPayoutAdd($data, $isNew)
	{
	}
}
