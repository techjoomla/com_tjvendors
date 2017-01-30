<?php
/**
 * @version    SVN: 
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Tjvendors model.
 *
 * @since  1.6
 */
class TjvendorsModelVendorFee extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_TJVENDORS';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_tjvendors.vendorfee';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Vendorfee', $prefix = 'TjvendorsTable', $config = array())
	{
		$db = JFactory::getDbo();
		$tables = $db->getTableList();

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_tjvendors.vendorfee', 'vendorfee',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk)
	{
		$data = parent::getItem($pk);
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendor = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		$vendorDetail = $TjvendorsModelVendor->getItem();
		$data->vendor_title = $vendorDetail->vendor_title;
		$data->client = $vendorDetail->vendor_client;

		return $data;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tjvendors.edit.vendorfee.data', array());
		$input = JFactory::getApplication()->input;

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}
		}

		$data = (array) $this->item;

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   STRING  $vendorId  The vendorId of the Vendor_id.
	 * 
	 * @param   STRING  $currency  The currency of the Vendor_id.
	 * 
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getVendorFeeId($vendorId,$currency)
	{
		if (!empty($vendorId) && !empty($currency))
		{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__tjvendors_vendors'));
		$query->where($db->quoteName('vendor_id') . '=' . $vendorId);
		$query->where($db->quoteName('currency') . '=' . $currency);
		$db->setQuery($query);

		return $db->loadResult();
		}

	return false;
	}

	/**
	 * Method for save user specific %commission, flat commission, client
	 *
	 * @param   Array  $data  Data
	 *
	 * @return id
	 */
	public function save($data)
	{
		$table = $this->getTable();
		$db = JFactory::getDBO();
		$input  = JFactory::getApplication()->input;
		$app  = JFactory::getApplication();

		if ($data['vendor_id'] != 0)
		{
			// Attempt to save data
			if (parent::save($data))
			{
				return true;
			}
		}
		else
		{
			return false;
		}

		return false;
	}
}
