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
class PlgTjvendorsTjvendors extends CMSPlugin
{
	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  1.4.3
	 */
	protected $autoloadLanguage = true;

	/**
	 * On saving vendor data
	 *
	 * Method is called after vendor data is stored in the database.
	 *
	 * @param   array  $data  Holds the new vendor data.
	 *
	 * @return  void
	 *
	 * @since   1.4.3
	 */
	public function tjVendorOnAfterVendorSave($data)
	{
	}

	/**
	 * On after payout paid
	 *
	 * Method is called after payout is added.
	 *
	 * @param   int    $id    Payout payment ID
	 * @param   array  $data  Payout data
	 *
	 * @return  void
	 *
	 * @since   1.4.3
	 */
	public function tjVendorOnAfterPayoutPaid($id, $data)
	{
	}

	/**
	 * On after payout status is changed
	 *
	 * Method is called after payout status is changed.
	 *
	 * @param   object  $object  Holds the payout data.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjVendorOnPayoutStatusChange($object = null)
	{
	}
}
