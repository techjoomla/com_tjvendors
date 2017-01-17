<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla  <contact@techjoomla.com>
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
class TjvendorsModelPayout extends JModelAdmin
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
	public $typeAlias = 'com_tjvendors.payout';

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
	public function getTable($type = 'payout', $prefix = 'TjvendorsTable', $config = array())
	{
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
			'com_tjvendors.payout', 'payout',
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
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tjvendors.edit.payout.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
			$TjvendorsModelVendor = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
			$vendorDetail = $TjvendorsModelVendor->getItem();
			$this->item->vendor_title = $vendorDetail->vendor_title;
		}

		return $this->item;
	}

	/**
	 * Method for saving the pending payable amount
	 *
	 * @param   Array  $data  Data
	 *
	 * @return id
	 */
	public function save($data)
	{
		$db = JFactory::getDbo();
		$input  = JFactory::getApplication()->input;
		$items = parent::getItem($pk = null);
		$items = $this->getItem($data['id']);
		$data['debit'] = $data['total'];
		$pending_amount = $input->get('pendingamount', '', 'INTEGER');
		$data['total'] = $pending_amount - $data['debit'];
		$data['transaction_time'] = JFactory::getDate()->toSql();
		$data['reference_order_id'] = $items->reference_order_id;
		$data['client'] = $items->client;
		$data['transaction_id'] = $items->vendor_id . $items->client . $items->currency;
		$data['id'] = '';
		$data['vendor_id'] = $items->vendor_id;

		if (parent::save($data))
		{
			$id = (int) $this->getState($this->getName() . '.id');
			$payout_update = new stdClass;

			// Must be a valid primary key value.
			$payout_update->id = $id;
			$payout_update->transaction_id = $data['transaction_id'] . $payout_update->id;

			// Update their details in the users table using id as the primary key.
			$result = JFactory::getDbo()->updateObject('#__tjvendors_passbook', $payout_update, 'id');

			return true;
		}

		return false;
	}
}
