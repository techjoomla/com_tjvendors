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

		// $this->jgiveFrontendHelper = new jgiveFrontendHelper;

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

		return;
	}

	/**
	 * Send mails when vendor is editted
	 *
	 * @param   OBJECT  $vendorDetails  vender details
	 *
	 * @return void
	 */
	public function onAfterVendorStateChange($vendorDetails)
	{
		// $campaignItemid = $this->jgiveFrontendHelper->getItemId('index.php?option=com_jgive&view=campaigns&layout=my');

		$promoterEmailObj = new stdClass;
		$promoterEmailObj->email = $vendorDetails->email;
		$promoterRecipients[] = $promoterEmailObj;

		$promoterkey = "approvalOnCampaignMailToPromoter";

		$replacements = new stdClass;
/*
		$mycampaigns = 'index.php?option=com_jgive&view=campaigns&layout=my&Itemid=' . $campaignItemid;
		$mycampaignsLink = JUri::root() . substr(JRoute::_($mycampaigns), strlen(JUri::base(true)) + 1);
		$campaignDetails->mycampaigns = $mycampaignsLink;
*/
		$replacements->info = $this->siteinfo;
		$replacements->campaign = $vendorDetails;

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new JRegistry;
		$options->set('info', $vendorDetails);

		// Mail to Promoter
		$this->tjnotifications->send($this->client, $promoterkey, $promoterRecipients, $replacements, $options);

		return;
	}

	/**
	 * Method newDonationStatus.
	 *
	 * @param   ARRAY  $donationDetails  Donation Details.
	 *
	 * @return  void.
	 *
	 * @since	1.8
	 */
	public function newDonationStatus($donationDetails)
	{
		$replacements = $donation = $campaign = new stdClass;
		$orderstatus = '';

		switch ($donationDetails['payment']->status)
		{
			case 'C':
				$orderstatus = JText::_('COM_JGIVE_CONFIRMED');
				break;
			case 'RF':
				$orderstatus = JText::_('COM_JGIVE_REFUND');
				break;
			case 'P':
				$orderstatus = JText::_('COM_JGIVE_PENDING');
				break;
			case 'E':
				$orderstatus = JText::_('COM_JGIVE_CANCELED');
				break;
			case 'D':
				$orderstatus = JText::_('COM_JGIVE_DENIED');
				break;
		}

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jgive/tables');
		$campaignDetails = JTable::getInstance('campaign', 'JgiveTable');
		$campaignDetails->load(array('id' => $donationDetails['payment']->campaign_id));
		$creator_id = $campaignDetails->creator_id;

		$creator_id = $donationDetails['campaign']->creator_id;
		$creator = JFactory::getUser($creator_id);

		JLoader::import('components.com_jgive.helper', JPATH_SITE);
		$jgiveFrontendHelper = new JgiveFrontendHelper;

		$myDonationsItemid = $this->jgiveFrontendHelper->getItemId('index.php?option=com_jgive&view=donations&layout=my');
		$myDonation = 'index.php?option=com_jgive&view=donations&layout=my';
		$myDonationLink = JUri::root() . substr(JRoute::_($myDonation), strlen(JUri::base(true)) + 1);

		$donationDetailsSubject->sitename = $this->sitename;
		$donationDetailsSubject->order_id = $donationDetails['payment']->order_id;
		$donationDetails['payment']->amount = $jgiveFrontendHelper->getFormattedPrice($donationDetails['payment']->amount);
		$donationDetails['payment']->anonymous = ($donationDetails['payment']->annonymous_donation) ? JText::_('COM_JGIVE_YES') : JText::_('COM_JGIVE_NO');
		$donationDetails['payment']->donationStatus = $orderstatus;
		$donationDetails['payment']->donoremail = $this->user->email ? $this->user->email : $donationDetails['donor']->email;
		$donationDetails['payment']->donorname = $this->user->name ? $this->user->name : $donationDetails['donor']->first_name;
		$donationDetails['payment']->mydonations = $myDonationLink;

		$donationDetails['campaign']->goal_amount = $jgiveFrontendHelper->getFormattedPrice($donationDetails['campaign']->goal_amount);
		$donationDetails['campaign']->amount_received = $jgiveFrontendHelper->getFormattedPrice($donationDetails['campaign']->amount_received);
		$donationDetails['campaign']->remaining_amount = $jgiveFrontendHelper->getFormattedPrice($donationDetails['campaign']->remaining_amount);

		$replacements->info = $this->siteinfo;
		$replacements->payment = $donationDetails['payment'];
		$replacements->campaign = $donationDetails['campaign'];
		$replacements->donor = $donationDetails['donor'];
		$replacements->promoter = $creator;

		$options = new JRegistry;
		$options->set('subject', $donationDetailsSubject);

		/* Mail to Admin user*/
		$adminEmailObj = new stdClass;
		$adminEmailObj->email = (!empty($this->jgiveparams->get('email'))) ? $this->jgiveparams->get('email') : $this->siteConfig->get('mailfrom');
		$adminRecipients = self::createRecipient($adminEmailObj->email);
		$adminRecipients[] = $adminRecipients;
		$paidDonationInvestmentAdminKey = ($donationDetails['campaign']->type == 'donation') ? 'paidDonationMailToAdmin' : 'paidInvestementMailToAdmin';
		$this->tjnotifications->send($this->client, $paidDonationInvestmentAdminKey, $adminRecipients, $replacements, $options);

		/* Mail to Donor/investor*/
		$donorEmailObj = new stdClass;
		$donorEmailObj->email = $donationDetails['donor']->email ? $donationDetails['donor']->email : $this->user->email;
		$donorEmailObj->donorname = $donationDetails['donor']->first_name ? $donationDetails['donor']->first_name : $this->user->name;
		$donorrecipients[] = $donorEmailObj;
		$paidDonationInvestmentDonorKey = ($donationDetails['campaign']->type == 'donation') ? 'paidDonationMailToDonor' : 'paidInvestmentMailToDonor';
		$this->tjnotifications->send($this->client, $paidDonationInvestmentDonorKey, $donorrecipients, $replacements, $options);

		/* Mail to Campaign Promoter*/
		$promoterEmailObj = new stdClass;
		$promoterEmailObj->email = $creator->email;
		$promoterrecipients[] = $promoterEmailObj;
		$paidDonationInvestmentPromoterKey = ($donationDetails['campaign']->type == 'donation')
		? 'paidDonationMailToPromoter' : 'paidInvestmentMailToPromoter';
		$this->tjnotifications->send($this->client, $paidDonationInvestmentPromoterKey, $promoterrecipients, $replacements, $options);

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
