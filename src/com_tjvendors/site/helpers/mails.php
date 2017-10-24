<?php

/**
 * @package    TJvendors
 * @author     TechJoomla | <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://www.techjoomla.com
 */

defined('_JEXEC') or die;

/**
 * Class TjvendorMailsHelper
 *
 * @since  2.1
 */
class TjvendorMailsHelper
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$app = JFactory::getApplication();
		$this->tjvendorsparams = JComponentHelper::getParams('com_tjvendors');
		$this->siteConfig = JFactory::getConfig();
		$this->sitename = $this->siteConfig->get('sitename');
		$this->siteadminname = $this->siteConfig->get('fromname');
		$this->user = JFactory::getUser();
		$this->client = "com_tjvendors";
		$this->tjnotifications = new Tjnotifications;
		$this->siteinfo = new stdClass;
		$this->siteinfo->sitename	= $this->sitename;
		$this->siteinfo->adminname = JText::_('COM_JGIVE_SITEADMIN');
	}

	/**
	 * Send mails when campaign is created
	 *
	 * @param   BOOLEAN  $vendorDetails  Vender Detail
	 *
	 * @return void
	 */
	public function onAfterVendorCreate($vendorDetails)
	{
		$adminEmail = (!empty($this->tjvendorsparams->get('email'))) ? $this->tjvendorsparams->get('email') : $this->siteConfig->get('mailfrom');
		$adminRecipients = self::createRecipient($adminEmail);

		$vendor_approval = $this->tjvendorsparams->get('vendor_approval');

		$adminkey = ($vendor_approval) ? "createVendorMailToAdminWaitingForApproval" : "createVendorMailToAdmin";
		$vendorerkey = ($vendor_approval) ? "createVendorMailToOwnerWaitingForApproval" : "createVendorMailToOwner";

		$promoterEmailObj = new stdClass;
		$vendorer = JFactory::getUser($vendorDetails->user_id);
		$promoterEmailObj->email = $vendorer->email;
		$promoterRecipients[] = $promoterEmailObj;

		$replacements = new stdClass;

// "index.php?option=com_tjvendors&layout=edit&vendor_id=" . $vendorDetails->vendor_id . "&client='" . $vendorDetails->vendor_client . "'";

/*
		$myCampsItemid = $this->jgiveFrontendHelper->getItemId('index.php?option=com_jgive&view=campaigns&layout=my');
		$myCamps = 'index.php?option=com_jgive&view=campaigns&layout=my&Itemid=' . $myCampsItemid;
		$myCampsLink     = JUri::root() . substr(JRoute::_($myCamps), strlen(JUri::base(true)) + 1);
		$campaignDetails->mycampaigns = $myCampsLink;

		$allCampsItemid = $this->jgiveFrontendHelper->getItemId('index.php?option=com_jgive&view=campaigns&layout=all');
		$allCamps = 'index.php?option=com_jgive&view=campaigns&layout=all&Itemid=' . $allCampsItemid;
		$allCampsLink = JUri::root() . substr(JRoute::_($allCamps), strlen(JUri::base(true)) + 1);
		$campaignDetails->allcampaigns = $allCampsLink;
		$campaignDetails->sitename = $this->sitename;
*/

		$vendorDetails->sitename = $this->sitename;
		$vendorDetails->adminname = JText::_('COM_JGIVE_SITEADMIN');
		$replacements->info = $vendorDetails;
		$replacements->vendorer = $vendorer;

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new JRegistry;
		$options->set('info', $vendorDetails);

		// Mail to site admin
		$this->tjnotifications->send($this->client, $adminkey, $adminRecipients, $replacements, $options);

		// Mail to Promoter
		$this->tjnotifications->send($this->client, $vendorerkey, $promoterRecipients, $replacements, $options);

		return;
	}

	/**
	 * Send mails when vendor is editted
	 *
	 * @param   OBJECT  $vendorDetails  vendor details
	 *
	 * @return void
	 */
	public function onAfterVendorEdit($vendorDetails)
	{
		$adminkey = "editVendorMailToAdmin";

		$adminEmailObj = new stdClass;

		$adminEmail = (!empty($this->tjvendorsparams->get('email'))) ? $this->tjvendorsparams->get('email') : $this->siteConfig->get('mailfrom');
		$adminRecipients = self::createRecipient($adminEmail);

		$replacements = new stdClass;
/*
		$singleCampaignItemid = $this->jgiveFrontendHelper->getItemId(
			'index.php?option=com_jgive&view=campaign&layout=single&cid=' . $campaignDetails->id
		);
		$singleCampaign = 'index.php?option=com_jgive&view=campaign&layout=single&cid='
		. $campaignDetails->id . '&Itemid=' . $singleCampaignItemid;
		$singleCampaignLink = JUri::root() . substr(JRoute::_($singleCampaign), strlen(JUri::base(true)) + 1);
		$campaignDetails->campaignDetailed = $singleCampaignLink;
*/

		$vendorDetails->sitename = $this->sitename;
		$vendorDetails->adminname = JText::_('COM_JGIVE_SITEADMIN');

		$replacements->info = $vendorDetails;
		$replacements->vendorer = JFactory::getUser($vendorDetails->user_id);

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new JRegistry;
		$options->set('info', $vendorDetails);

		// Mail to site admin
		$this->tjnotifications->send($this->client, $adminkey, $adminRecipients, $replacements, $options);

		// Find admin has approved vendor, and add a new key
		// if ()
		$approvalkey = "approvalOnVendorMailToOwner";
		$promoterEmailObj = new stdClass;
		$promoterEmailObj->email = $vendorDetails->email;
		$promoterRecipients[] = $promoterEmailObj;

		// $this->tjnotifications->send($this->client, $approvalkey, $promoterRecipients, $replacements, $options);

		return;
	}

	/**
	 * Send mails when vendor payout is generated
	 *
	 * @param   OBJECT  $payoutDetails  vendor payout details
	 *
	 * @return void
	 */
	public function onAfterPayoutCreate($payoutDetails)
	{
		$vendorkey = "vendorPayoutMailToPromoter";

		$payoutDetails->sitename = $this->sitename;
		$payoutDetails->adminname = JText::_('COM_JGIVE_SITEADMIN');

		$replacements = new stdClass;
		$replacements->info = $payoutDetails;
		$replacements->vendorer = JFactory::getUser($payoutDetails->user_id);

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new JRegistry;
		$options->set('info', $payoutDetails);

		$promoterEmailObj = new stdClass;
		$promoterEmailObj->email = $payoutDetails->email;
		$promoterRecipients[] = $promoterEmailObj;

		$this->tjnotifications->send($this->client, $vendorkey, $promoterRecipients, $replacements, $options);

		return;
	}

	/**
	 * Method to create recipient array
	 *
	 * @param   ARRAY  $emailObject  Contains email object
	 *
	 * @return  void.
	 *
	 * @since	1.8
	 */
	public function createRecipient($emailObject)
	{
		$adminRecipients = explode(',', $emailObject);

		$finalEmailRecipient = [];

		if (!empty($adminRecipients))
		{
			foreach ($adminRecipients as $adminRecipient)
			{
				$adminEmailObj = new stdClass;
				$adminEmailObj->email = $adminRecipient;
				$finalEmailRecipient[] = $adminEmailObj;
			}
		}

		return $finalEmailRecipient;
	}
}
