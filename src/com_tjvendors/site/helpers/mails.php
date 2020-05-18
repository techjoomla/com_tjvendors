<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

jimport('techjoomla.tjnotifications.tjnotifications');

/**
 * Class TjvendorMailsHelper
 *
 * @since  2.1
 */
class TjvendorsMailsHelper
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$app = Factory::getApplication();
		$this->tjvendorsparams = ComponentHelper::getParams('com_tjvendors');
		$this->siteConfig = Factory::getConfig();
		$this->sitename = $this->siteConfig->get('sitename');
		$this->siteadminname = $this->siteConfig->get('fromname');
		$this->user = Factory::getUser();
		$this->client = "com_tjvendors";
		$this->tjnotifications = new Tjnotifications;
		$this->siteinfo = new stdClass;
		$this->siteinfo->sitename	= $this->sitename;
		$this->siteinfo->adminname = Text::_('COM_TJVENDORS_SITEADMIN');

		JLoader::import('components.com_tjvendors.helpers.fronthelper', JPATH_SITE);
		$this->tjvendorFrontHelper = new TjvendorFrontHelper;
	}

	/**
	 * Send mails when campaign is created
	 *
	 * @param   OBJECT  $vendorDetails  Vender Detail
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
		$vendorer = Factory::getUser($vendorDetails->user_id);
		$promoterEmailObj->email = $vendorer->email;
		$promoterRecipients[] = $promoterEmailObj;

		$allVendors = 'index.php?option=com_tjvendors&view=vendors&client=' . $vendorDetails->vendor_client;
		$allVendorsLink = Uri::root() . 'administrator/' . $allVendors;
		$vendorDetails->allVendors = $allVendorsLink;

		$vendorItemID = $this->tjvendorFrontHelper->getItemId(
		'index.php?option=com_tjvendors&view=vendor&layout=edit&client=' . $vendorDetails->vendor_client
		);
		$myVendor = 'index.php?option=com_tjvendors&view=vendor&layout=edit&client='
		. $vendorDetails->vendor_client . '&vendor_id=' . $vendorDetails->vendor_id . '&Itemid=' . $vendorItemID;
		$myVendorLink = Uri::root() . substr(Route::_($myVendor), strlen(Uri::base(true)) + 1);
		$vendorDetails->myVendor = $myVendorLink;

		$replacements = new stdClass;
		$vendorDetails->sitename = $this->sitename;
		$vendorDetails->adminname = Text::_('COM_TJVENDORS_SITEADMIN');
		$replacements->info = $vendorDetails;
		$replacements->vendorer = $vendorer;

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new Registry;
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
		$replacements = new stdClass;
		$vendorDetails->sitename = $this->sitename;
		$vendorDetails->adminname = Text::_('COM_TJVENDORS_SITEADMIN');
		$loggedInUser = Factory::getUser()->id;

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
		$vendorData = Table::getInstance('Vendorclientxref', 'TjvendorsTable');
		$vendorData->load(array('vendor_id' => $vendorDetails->vendor_id));
		$vendor_client = $vendorData->client;

		$vendorDetails->vendorClient = $this->tjvendorFrontHelper->getClientName($vendor_client);
		$replacements->info = $vendorDetails;
		$replacements->vendorer = Factory::getUser($vendorDetails->user_id);

		$ccMail = $this->siteConfig->get('mailfrom');
		$options = new Registry;
		$options->set('info', $vendorDetails);

		$vendor_approval = $this->tjvendorsparams->get('vendor_approval');

		// Find admin has approved vendor, and add a new key
		if ($vendor_approval && $vendorDetails->approved == 1)
		{
			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
			$vendorData = Table::getInstance('Vendor', 'TjvendorsTable');
			$vendorData->load(array('vendor_id' => $vendorDetails->vendor_id));
			$vendorUserDetails = Factory::getUser($vendorData->user_id);

			$approvalkey = "approvalOnVendorMailToOwner";
			$promoterEmailObj = new stdClass;
			$promoterEmailObj->email = $vendorUserDetails->email;
			$promoterRecipients[] = $promoterEmailObj;
			$replacements->vendor_user = $vendorUserDetails;
			$replacements->vendor_data = $vendorData;
			$options->set('vendor_data', $vendorData);

			$this->tjnotifications->send($this->client, $approvalkey, $promoterRecipients, $replacements, $options);
		}
		elseif ($vendorDetails->user_id === $loggedInUser)
		{
			$adminEmailObj = new stdClass;
			$adminEmail = (!empty($this->tjvendorsparams->get('email'))) ? $this->tjvendorsparams->get('email') : $this->siteConfig->get('mailfrom');
			$adminRecipients = self::createRecipient($adminEmail);

			$adminkey = "editVendorMailToAdmin";

			$this->tjnotifications->send($this->client, $adminkey, $adminRecipients, $replacements, $options);
		}

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
