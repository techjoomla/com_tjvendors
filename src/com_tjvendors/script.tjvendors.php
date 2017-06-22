<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @version   $Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');

if (!defined('DS'))
{
	define('DS',DIRECTORY_SEPARATOR);
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
		$check = $this->checkTableExists('tj_vendors');

		if ($check)
		{
			$this->updateData();
		}
	}

	function installSqlFiles($parent)
	{
		$db = JFactory::getDBO();

		// Lets create the table
		$this->runSQL($parent, 'install.mysql.utf8.sql');
	}

	function runSQL($parent,$sqlfile)
	{
		$db = JFactory::getDBO();

		// Obviously you may have to change the path and name if your installation SQL file ;)
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root').DS.'administrator'.DS.'sql'.DS.$sqlfile;
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'sql'.DS.$sqlfile;
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

	function updateData()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tj_vendors'));
		$db->setQuery($query);
		$oldVendorsData = $db->loadObjectList();

		if (!empty($oldVendorsData))
		{
			foreach ($oldVendorsData as $oldData)
			{
				$newVendorData = new stdClass;
				$newVendorData->user_id = $oldData->user_id;
				$newVendorData->vendor_id = $oldData->id;
				$newVendorData->state = 1;
				$newVendorData->vendor_title = JFactory::getUser($oldData->user_id)->name;
				// Insert the object into the user profile table.
				$result = JFactory::getDbo()->insertObject('#__tjvendors_vendors', $newVendorData);

				$newXrefData = new stdClass;
				$newXrefData->vendor_id = $oldData->id;
				$newXrefData->id = $oldData->id;
				$newXrefData->client = 'com_jticketing';
				$result = JFactory::getDbo()->insertObject('#__vendor_client_xref', $newXrefData);

				$newFeeData = new stdClass;
				$newFeeData->vendor_id = $oldData->id;
				$newFeeData->id = $oldData->id;
				$newFeeData->client = 'com_jticketing';
				$newFeeData->currency = 'USD';
				$newFeeData->percent_commission = $oldData->percent_commission;
				$newFeeData->flat_commission = $oldData->flat_commission;
				$result = JFactory::getDbo()->insertObject('#__tjvendors_fee', $newFeeData);
			}
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

		$query =" SELECT table_name
		 FROM information_schema.tables
		 WHERE table_schema='" . $dbname . "'
		 AND table_name='" . $dbprefix . $table . "'";

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

}
