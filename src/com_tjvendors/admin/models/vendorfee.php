<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.modeladmin');

/**
 * Tjvendors model.
 *
 * @since  1.6
 */
class TjvendorsModelVendorFee extends AdminModel
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
	 * @return    Table    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Vendorfee', $prefix = 'TjvendorsTable', $config = array())
	{
		$db     = Factory::getDbo();
		$tables = $db->getTableList();

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form  A Form object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_tjvendors.vendorfee', 'vendorfee', array('control' => 'jform', 'load_data' => $loadData));

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
	public function getItem($pk = null)
	{
		$data = parent::getItem($pk);
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendor = BaseDatabaseModel::getInstance('Vendor', 'TjvendorsModel');
		$vendorDetail         = $TjvendorsModelVendor->getItem();
		$data->vendor_title   = $vendorDetail->vendor_title;

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
		$data = Factory::getApplication()->getUserState('com_tjvendors.edit.vendorfee.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$this->item->currency_unchange = $this->item->currency;
			$data = $this->item;
		}

		return $data;
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
		$isNew             = (empty($data['id']))? true : false;
		$app               = Factory::getApplication();
		$table             = $this->getTable();
		$db                = Factory::getDbo();
		$input             = $app->input;
		$data['vendor_id'] = $input->get('vendor_id', '', 'INTEGER');
		$uniqueCurrency    = TjvendorsHelper::checkUniqueCurrency($data['currency'], $data['vendor_id'], $data['client']);

		if (!empty($uniqueCurrency))
		{
			if (parent::save($data))
			{
				if (empty($data['id']))
				{
					$data['id'] = (int) $this->getState($this->getName() . '.id');
				}

				$dispatcher = JDispatcher::getInstance();
				PluginHelper::importPlugin('tjvendors');
				$dispatcher->trigger('tjVendorsOnAfterVendorFeeSave', array($data, $isNew));

				return true;
			}
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_TJVENDORS_VENDORFEE_DUPLICATE_CURRENCY'), 'error');
		}

		return false;
	}
}
