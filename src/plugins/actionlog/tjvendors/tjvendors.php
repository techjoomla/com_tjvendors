<?php
/**
 * @package     TJVendors
 * @subpackage  Actionlog.tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');

/**
 * TJVendors Actions Logging Plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgActionlogTjvendors extends JPlugin
{
	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * On saving vendor data logging method
	 *
	 * Method is called after vendor data is stored in the database.
	 *
	 * @param   Object   $vendorData  Holds the vendor data.
	 * @param   boolean  $isNew       True if a new vendor is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjVendorsOnAfterVendorSave($vendorData, $isNew)
	{
		if (!$this->params->get('logActionForVendorSave', 1))
		{
			return;
		}

		$app      = JFactory::getApplication();
		$context  = $app->input->get('option');
		$jUser    = JFactory::getUser();
		$userId   = $jUser->id;
		$userName = $jUser->username;

		// If admin create user as vendor from backend
		if ($app->isAdmin())
		{
			$vendorInfor  = JFactory::getUser($vendorData['user_id']);
			$vendorID     = $vendorInfor->id;
			$vendorUserName = $vendorInfor->username;
			$action       = ($isNew) ? 'add' : 'update';

			// Create New Vendor
			if ($isNew)
			{
				$messageLanguageKey = ($vendorData['vendor_client']) ? 'PLG_ACTIONLOG_TJVENDORS_VENDOR_CLIENT_SAVE' : 'PLG_ACTIONLOG_TJVENDORS_VENDOR_SAVE';

				if ($vendorData['vendor_client'])
				{
					$message = array(
						'action'             => $action,
						'type'               => 'PLG_ACTIONLOG_TJVENDORS_TYPE_VENDOR',
						'clientname'         => $vendorData['vendor_client'],
						'clientlink'         => 'index.php?option=' . $vendorData['vendor_client'],
						'vendorusername'	 =>	$vendorUserName,
						'vendoruserlink'	 =>	'index.php?option=com_users&task=user.edit&id=' . $vendorID,

						'userid'             => $userId,
						'username'           => $userName,
						'accountlink'        => 'index.php?option=com_users&task=user.edit&id=' . $userId,
					);
				}
				else
				{
					$message = array(
						'action'             => $action,
						'type'               => 'PLG_ACTIONLOG_TJVENDORS_TYPE_VENDOR',
						'vendorusername'	 =>	$vendorUserName,
						'vendoruserlink'	 =>	'index.php?option=com_users&task=user.edit&id=' . $vendorID,
						'userid'             => $userId,
						'username'           => $userName,
						'accountlink'        => 'index.php?option=com_users&task=user.edit&id=' . $userId,
					);
				}
			}
			// Edit Vendor
			else
			{
				$messageLanguageKey = 'PLG_ACTIONLOG_TJVENDORS_VENDOR_CLIENT_UPDATE';
				$message = array(
					'action'             => $action,
					'type'               => 'PLG_ACTIONLOG_TJVENDORS_TYPE_VENDOR',
					'clientname'         => $vendorData['vendor_client'],
					'clientlink'         => 'index.php?option=' . $vendorData['vendor_client'],
					'vendorname'	     =>	$vendorData['vendor_title'],
					'vendorusername'	 =>	$vendorUserName,
					'vendoruserlink'	 =>	'index.php?option=com_users&task=user.edit&id=' . $vendorID,

					'userid'             => $userId,
					'username'           => $userName,
					'accountlink'        => 'index.php?option=com_users&task=user.edit&id=' . $userId,
				);
			}
		}
		// If register user create vendor profile for self from frontend
		else
		{
			$messageLanguageKey = ($isNew) ? 'PLG_ACTIONLOG_TJVENDORS_REGISTER_VENDOR_SAVE' : 'PLG_ACTIONLOG_TJVENDORS_REGISTER_VENDOR_UPDATE';
			$action = ($isNew) ? 'add' : 'update';

			if ($isNew)
			{
				$message = array(
					'action'             => $action,
					'type'               => 'PLG_ACTIONLOG_TJVENDORS_TYPE_VENDOR',
					'clientname'         => $vendorData['vendor_client'],
					'clientlink'         => 'index.php?option=' . $vendorData['vendor_client'],
					'userid'             => $userId,
					'username'           => $userName,
					'accountlink'        => 'index.php?option=com_users&task=user.edit&id=' . $userId,
				);
			}
			else
			{
				$message = array(
					'action'             => $action,
					'type'               => 'PLG_ACTIONLOG_TJVENDORS_TYPE_VENDOR',
					'clientname'         => $vendorData['vendor_client'],
					'clientlink'         => 'index.php?option=' . $vendorData['vendor_client'],
					'userid'             => $userId,
					'username'           => $userName,
					'accountlink'        => 'index.php?option=com_users&task=user.edit&id=' . $userId,
				);
			}
		}

		$this->addLog(array($message), $messageLanguageKey, $context, $userId);
	}

	/**
	 * On after changing vendor state logging method
	 *
	 * Method is called after vendor state is changed from  the database.
	 *
	 * @param   Array    $pks    Holds the vendors id
	 * @param   Integer  $state  0-indicate unpublish 1-indicate publish.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjVendorsOnAfterVendorStateChange($pks, $state)
	{
		if (!$this->params->get('logActionForVendorStateChange', 1))
		{
			return;
		}
		
		$jUser     = JFactory::getUser();
		$userId    = $jUser->id;
		$userName  = $jUser->username;
		$context   = JFactory::getApplication()->input->get('option');
		$tjvendorsTableVendor = JTable::getInstance('vendor', 'TjvendorsTablevendor', array());
		
		switch ($state)
		{
			case 0:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJVENDORS_VENDOR_UNPUBLISHED';
				$action             = 'unpublish';
				break;
			case 1:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJVENDORS_VENDOR_PUBLISHED';
				$action             = 'publish';
				break;
			default:
				$messageLanguageKey = '';
				$action             = '';
				break;
		}
		
		// User admin has published vendor of client JT
		// User <a href=\"{accountlink}\">{username}</a> has published a {vendorname} {type} 
		// User <a href=\"{accountlink}\">{username}</a> has unpublished a {vendorname} {type} 
		foreach ($pks as $vendorID)
		{		
			$this->addLog(array($message), $messageLanguageKey, $context, $userId);
		}
	}

	/**
	 * On after deleting vendor data logging method
	 *
	 * Method is called after vendor data is deleted from  the database.
	 *
	 * @param   string  $context  com_tjvendors.
	 * @param   Object  $table    Holds the vendor data.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjvendorOnAfterVendorDelete($vendorData)
	{
		// User admin has deleted vendor "Vendor Name" for client JT
		// User admin has deleted vendor "Vendor Name"
		if (!$this->params->get('logActionForVendorDelete', 1))
		{
			return;
		}
	}

	/**
	 * On saving fee against vendor logging method
	 *
	 * Method is called after fee data is stored against vendor in the database.
	 *
	 * @param   Object   $vendorFeeData  Holds the vendor fee data.
	 * @param   boolean  $isNew          True if a new vendor is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjVendorsOnAfterVendorFeeSave($vendorFeeData, $isNew)
	{
		// User admin has added/updated vendor "Vendor Name" fee for client JT
		// User admin has added/updated vendor "Vendor Name" fee

		if (!$this->params->get('logActionForVendorFeeSave', 1))
		{
			return;
		}
	}

	/**
	 * Proxy for ActionlogsModelUserlog addLog method
	 *
	 * This method adds a record to #__action_logs contains (message_language_key, message, date, context, user)
	 *
	 * @param   array   $messages            The contents of the messages to be logged
	 * @param   string  $messageLanguageKey  The language key of the message
	 * @param   string  $context             The context of the content passed to the plugin
	 * @param   int     $userId              ID of user perform the action, usually ID of current logged in user
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		JLoader::register('ActionlogsModelActionlog', JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php');

		/* @var ActionlogsModelActionlog $model */
		$model = JModelLegacy::getInstance('Actionlog', 'ActionlogsModel');
		$model->addLog($messages, $messageLanguageKey, $context, $userId);
	}
}
