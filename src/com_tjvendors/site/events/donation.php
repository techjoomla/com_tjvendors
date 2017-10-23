<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    JGive
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgive.helpers.mails', JPATH_SITE);
JLoader::import('components.com_jgive.helpers.donations', JPATH_SITE);

/**
 * Jgive triggers class for campaign.
 *
 * @since  1.6
 */
class JGiveTriggerDonation
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$app    = JFactory::getApplication();
		$this->menu   = $app->getMenu();
		$this->jgiveparams = JComponentHelper::getParams('com_jgive');
		$this->siteConfig = JFactory::getConfig();
		$this->sitename = $this->siteConfig->get('sitename');
		$this->user = JFactory::getUser();
		$this->tjnotifications = new Tjnotifications;
		$this->jGiveMailsHelper = new JGiveMailsHelper;
	}

	/**
	 * Trigger for onDonationAfterSave
	 *
	 * @param   string   $donationDetails  Donation / Order Array
	 * @param   boolean  $isNew            true when new and false when existing
	 *
	 * @return  void
	 */
	public function onDonationAfterSave($donationDetails, $isNew)
	{
		switch ($isNew)
		{
			case true:
					/* If Donation order is new */
					$this->jGiveMailsHelper->newDonationStatus($donationDetails);
			break;

			case false:
				/* If Donation order is updated */
				$this->jGiveMailsHelper->updateDonationStatus($donationDetails);

				/* If Donation order status is confirmed */
				if ($donationDetails['payment']->status === 'C')
				{
					$this->jGiveMailsHelper->generateReceipt($donationDetails);
				}
			break;
		}

		return;
	}
}
