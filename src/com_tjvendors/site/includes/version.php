<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Version information class for the TJVendors.
 *
 * @since  __DEPLOY_VERSION__
 */
class TJVendorsVersion
{
	/**
	 * Product name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const PRODUCT = 'TJVendors!';

	/**
	 * Major release version.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const MAJOR_VERSION = 2;

	/**
	 * Minor release version.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const MINOR_VERSION = 8;

	/**
	 * Patch release version.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const PATCH_VERSION = 0;

	/**
	 * Extra release version info.
	 *
	 * This constant when not empty adds an additional identifier to the version string to reflect the development state.
	 * For example, for __DEPLOY_VERSION__ when this is set to 'dev' the version string will be `__DEPLOY_VERSION__-dev`.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const EXTRA_VERSION = '';

	/**
	 * Development status.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const DEV_STATUS = 'Stable';

	/**
	 * Code name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const CODENAME = 'TechJoomla';

	/**
	 * Release date.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const RELDATE = '27-January-2020';

	/**
	 * Release time.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const RELTIME = '16:22';

	/**
	 * Release timezone.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const RELTZ = 'GMT';

	/**
	 * Copyright Notice.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const COPYRIGHT = 'Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.';

	/**
	 * Link text.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	const URL = '<a href="https://www.techjoomla.com">TechJoomla!</a> is Joomla product dev.';

	/**
	 * Check if we are in development mode
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isInDevelopmentState()
	{
		return strtolower(self::DEV_STATUS) !== 'stable';
	}

	/**
	 * Compares two a "PHP standardized" version number against the current TJVendors version.
	 *
	 * @param   string  $minimum  The minimum version of the TJVendors which is compatible.
	 *
	 * @return  boolean True if the version is compatible.
	 *
	 * @link    https://www.php.net/version_compare
	 * @since   __DEPLOY_VERSION__
	 */
	public function isCompatible($minimum)
	{
		return version_compare(JVERSION, $minimum, 'ge');
	}

	/**
	 * Method to get the help file version.
	 *
	 * @return  string  Version suffix for help files.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getHelpVersion()
	{
		return '.' . self::MAJOR_VERSION . self::MINOR_VERSION;
	}

	/**
	 * Gets a "PHP standardized" version string for the current TJVendors.
	 *
	 * @return  string  Version string.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getShortVersion()
	{
		$version = self::MAJOR_VERSION . '.' . self::MINOR_VERSION . '.' . self::PATCH_VERSION;

		// Has to be assigned to a variable to support PHP 5.3 and 5.4
		$extraVersion = self::EXTRA_VERSION;

		if (!empty($extraVersion))
		{
			$version .= '-' . $extraVersion;
		}

		return $version;
	}

	/**
	 * Gets a version string for the current TJVendors with all release information.
	 *
	 * @return  string  Complete version string.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLongVersion()
	{
		return self::PRODUCT . ' ' . $this->getShortVersion() . ' '
			. self::DEV_STATUS . ' [ ' . self::CODENAME . ' ] ' . self::RELDATE . ' '
			. self::RELTIME . ' ' . self::RELTZ;
	}

	/**
	 * Returns the user agent.
	 *
	 * @param   string  $component    Name of the component.
	 * @param   bool    $mask         Mask as Mozilla/5.0 or not.
	 * @param   bool    $add_version  Add version afterwards to component.
	 *
	 * @return  string  User Agent.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getUserAgent($component = null, $mask = false, $add_version = true)
	{
		if ($component === null)
		{
			$component = 'Framework';
		}

		if ($add_version)
		{
			$component .= '/' . self::RELEASE;
		}

		// If masked pretend to look like Mozilla 5.0 but still identify ourselves.
		if ($mask)
		{
			return 'Mozilla/5.0 ' . self::PRODUCT . '/' . self::RELEASE . '.' . self::DEV_LEVEL . ($component ? ' ' . $component : '');
		}
		else
		{
			return self::PRODUCT . '/' . self::RELEASE . '.' . self::DEV_LEVEL . ($component ? ' ' . $component : '');
		}
	}

	/**
	 * Generate a media version string for assets
	 * Public to allow third party developers to use it
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function generateMediaVersion()
	{
		return md5($this->getLongVersion() . \JFactory::getConfig()->get('secret'));
	}

	/**
	 * Gets a media version which is used to append to TJVendors core media files.
	 *
	 * This media version is used to append to TJVendors core media in order to trick browsers into
	 * reloading the CSS and JavaScript, because they think the files are renewed.
	 * The media version is renewed after TJVendors core update, install, discover_install and uninstallation.
	 *
	 * @return  string  The media version.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getMediaVersion()
	{
		return $this->generateMediaVersion();
	}
}
