<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla  <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No access
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

class com_tjvendorsInstallerScript
{
	// Used to identify new install or update
	private $componentStatus = "install";

	/**
	* method to run before an install/update/uninstall method
	*
	* @return void
	*/
	function preflight($type, $parent)
	{
	}

	/**
	* Runs after install, update or discover_update
	* @param string $type install, update or discover_update
	* @param JInstaller $parent
	*/
	function postflight( $type, $parent )
	{
	}

	/**
	* method to install the component
	*
	* @return void
	*/
	function install($parent)
	{
		$this->installSqlFiles($parent);
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		$this->componentStatus = "update";
		$this->installSqlFiles($parent);
	}

	function installSqlFiles($parent)
	{
		$db = JFactory::getDBO();

		// Install country table(#__tj_country) if it does not exists
		$check = $this->checkTableExists('tjvendors_passbook');

		if (!$check)
		{
			// Lets create the table
			$this->runSQL($parent, 'install.mysql.utf8.sql');
		}
	}

	function checkTableExists($table)
	{
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();

		if (JVERSION >= '3.0')
		{
			$dbname = $config->get('db');
			$dbprefix = $config->get('dbprefix');
		}
		else
		{
			$dbname = $config->getValue('config.db');
			$dbprefix = $config->getvalue('config.dbprefix');
		}

		$query = " SELECT table_name
		FROM information_schema.tables
		WHERE table_schema = '" . $dbname . "'
		AND table_name ='" . $dbprefix . $table . "'";

		$db->setQuery($query);
		$check = $db->loadResult();

		if ($check)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	function runSQL($parent,$sqlfile)
	{
		$db = JFactory::getDBO();

		// Obviously you may have to change the path and name if your installation SQL file ;)
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . DS . 'administrator' . DS . 'sql' . DS . $sqlfile;
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . DS . 'sql' . DS . $sqlfile;
		}

		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);

		if ($buffer !== false)
		{
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);

						if (!$db->query())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}
	}
}
