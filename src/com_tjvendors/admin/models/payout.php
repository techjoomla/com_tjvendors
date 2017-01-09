<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 * 
	 * @param   array  $data  An optional array of data for the form to interogate.
	 * 
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	public function fetchingData($data)
	{
		$payout_id = (int) $this->getState($this->getName() . '.id');
		$object = new stdClass;

		// Must be a valid primary key value.
		$object->payout_id = $payout_id;
		$object->transaction_id = $data['transaction_id'] . $object->payout_id;

		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__tjvendors_passbook', $object, 'payout_id');
	}

	/**
	 * Method to add the amount when a product is purchased.
	 *
	 * @return   void.
	 *
	 * @since    1.6
	 */
	public function addCreditEntry()
	{
		$data = array('credit' => '450','debit_amount' => '0','vendor_id' => '839','currency' => 'Dollar','client' => 'Q2C','payout_id' => '');

		// To get the total pending amount of the particular vendor
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$subQuery = $db->getQuery(true);
		$subQuery->select('max(payout_id)')
			->from($db->quoteName('#__tjvendors_passbook'))
			->where($db->quoteName('vendor_id') . '=' . $data['vendor_id']);
		$query->select('*')
			->from($db->quoteName('#__tjvendors_passbook'))
			->where($db->quoteName('payout_id') . ' IN (' . $subQuery . ')');
		$db->setQuery($query);
		$row = $db->loadAssoc();
		$data['reference_order_id'] = rand();

		// Addition of credit amount to the pending amount
		$data['total'] = $row['total'] + $data['credit'];

		// Generating the transaction id
		$data['transaction_id'] = $data['vendor_id'] . $data['client'] . $data['currency'];
		$data['transaction_time'] = JFactory::getDate()->toSql();
		// Insert columns
		if (parent::save($data))
		{
			$this->fetchingData($data);
		}
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
		$table = $this->getTable();
		$db = JFactory::getDBO();
		$input  = JFactory::getApplication()->input;
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$data['debit'] = $formData->get('total');
		$pending_amount = $input->get('pendingamount', '', 'INTEGER');
		$data['total'] = $pending_amount - $data['debit'];
		$data['transaction_time'] = JFactory::getDate()->toSql();
		$items = parent::getItem($pk=null);
		$items = $this->getItem($data['payout_id']);
		$data['reference_order_id'] = $items->reference_order_id;
		$data['client'] = $items->client;
		$data['transaction_id'] = $items->vendor_id . $items->client . $items->currency;
		$data['payout_id'] = '';
//print_r($data);die;
		// Get a db connection.
		if (parent::save($data))
		{
			$payout_id = (int) $this->getState($this->getName() . '.id');
			$object = new stdClass;

			// Must be a valid primary key value.
			$object->payout_id = $payout_id;
			$object->transaction_id = $data['transaction_id'] . $object->payout_id;

			// Update their details in the users table using id as the primary key.
			$result = JFactory::getDbo()->updateObject('#__tjvendors_passbook', $object, 'payout_id');

			return true;
		}

		return false;
	}
}
