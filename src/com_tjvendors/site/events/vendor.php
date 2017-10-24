<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    TJ-vendors
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_tjvendors.helpers.mails', JPATH_SITE);

/**
 * TJ-vendors triggers class for vendors.
 *
 * @since  2.1
 */
class TjvendorTriggerVendor
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$app = JFactory::getApplication();
		$this->user = JFactory::getUser();
		$this->tjvendorMailsHelper = new TjvendorMailsHelper;
	}

	/**
	 * Trigger for vendor after save
	 *
	 * @param   int  $vendorDetails  Vendor Details
	 * @param   int  $isNew          isNew = true / !isNew = false
	 *
	 * @return  void
	 */
	public function onAfterVendorSave($vendorDetails, $isNew)
	{
		switch ($isNew)
		{
			/* New Vendor is created */
			case true:
					/* Send mail on Vendor create */
					$this->tjvendorMailsHelper->onAfterVendorCreate((object) $vendorDetails);
				break;

			/* Vendor is editted */
			case false:
					/* Send mail on Vendor edit */
					$this->tjvendorMailsHelper->onAfterVendorEdit((object) $vendorDetails);
				break;
		}

		return;
	}

	/**
	 * Trigger for vendor payout
	 *
	 * @param   int  $vendorDetails  Vendor Details
	 *
	 * @return  void
	 */
	public function onAfterVendorPayoutSave($payoutDetails)
	{
		/* Send mail on Vendor create */
		return $this->tjvendorMailsHelper->onAfterPayoutCreate((object) $payoutDetails);
	}

	/**
	 * Trigger for Campaign state change
	 *
	 * @param   int  $campaignDetails  Campaign Details
	 * @param   int  $isPublished      isPublished = 1 / !isPublished = 0
	 *
	 * @return  void
	 */
/*
	public function onCampaignStateChange($campaignDetails, $isPublished)
	{
		switch ($isPublished)
		{
			case 1:
				$this->jGiveMailsHelper->onAfterCampaignStateChange($campaignDetails);
				break;
		}

		return;
	}
*/
}
