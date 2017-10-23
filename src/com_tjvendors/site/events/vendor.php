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
JLoader::import('components.com_tjvendors.helpers.mails', JPATH_SITE);

// JLoader::import('components.com_jgive.helpers.donations', JPATH_SITE);

/**
 * Jgive triggers class for vendor.
 *
 * @since  1.6
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
		$app    = JFactory::getApplication();
		$this->menu   = $app->getMenu();
		$this->tjvendorsparams = JComponentHelper::getParams('com_tjvendors');
		$this->siteConfig = JFactory::getConfig();
		$this->sitename = $this->siteConfig->get('sitename');
		$this->user = JFactory::getUser();
		$this->tjnotifications = new Tjnotifications;
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
