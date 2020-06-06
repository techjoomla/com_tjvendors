<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Affiliate model.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsModelAffiliate extends AdminModel
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    __DEPLOY_VERSION__
	 */
	protected $text_prefix = 'COM_TJVENDORS';

	/**
	 * @var     string      Alias to manage history control
	 * @since   __DEPLOY_VERSION__
	 */
	public $typeAlias = 'com_tjvendors.affiliate';

	/**
	 * @var null  Item data
	 * @since  __DEPLOY_VERSION__
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable|Boolean    A database object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTable($type = 'Affiliates', $prefix = 'TjvendorsTable', $config = array())
	{
		// Load tables to fix - unable to load the vendors data using the model object,
		// When it is created outside the tjvendors component
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_tjvendors.affiliate', 'affiliate',
			array('control' => 'jform',
				'load_data' => $loadData,
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_tjvendors.edit.affiliate.data', array());

		if (empty($data))
		{
			$data = $this->getItem()->getProperties();
		}

		return $data;
	}

	/**
	 * Method to save a single record.
	 *
	 * @param   array  $data  Affiliate data
	 *
	 * @return  integer|boolean    true on success, false on failure.
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	public function save($data)
	{
		$pk   = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('affiliate.id');

		$tjvendorsAffiliate = TJVendors::affiliate($pk);

		// Bind the data.
		if (!$tjvendorsAffiliate->bind($data))
		{
			$this->setError($tjvendorsAffiliate->getError());

			return false;
		}

		$result = $tjvendorsAffiliate->save();

		// Store the data.
		if (!$result)
		{
			$this->setError($tjvendorsAffiliate->getError());

			return false;
		}

		return $result;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  TjvendorsAffiliate|boolean  Object on success, false on failure.
	 *
	 * @since    __DEPLOY_VERSION__
	 */
	public function getItem($pk = null)
	{
		$pk     = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		return TJVendors::affiliate($pk);
	}
}
