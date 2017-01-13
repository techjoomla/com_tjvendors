<?php
/**
 * @version    SVN: 
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Vendor controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendorFee extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
			$this->view_list = 'vendorfees';
		$this->input = JFactory::getApplication()->input;

		if (empty($this->client))
		{
			$this->client = $this->input->get('client', '');
		}

		parent::__construct();
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'vendor_id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&client=' . $this->client . '&curr[]=INR&curr[]=USD';

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&client=' . $this->client;

		return $append;
	}

	/**
	 * Function to add field data
	 *
	 * @return  void
	 */
	public function add()
	{
		$input = JFactory::getApplication()->input;
		$link = JRoute::_(
		'index.php?option=com_tjvendors&view=vendorfee&layout=edit&client=' . $input->get('client', '', 'STRING')
		. '&curr[]=INR&curr[]=USD', false
		);
		$this->setRedirect($link);
	}

	/**
	 * Function to edit field data
	 *
	 * @param   integer  $key  The primary key id for the item.
	 * 
	 * @return  void
	 */
	public function cancel($key = null)
	{
		$input = JFactory::getApplication()->input;
		$link = JRoute::_('index.php?option=com_tjvendors&view=vendorfees&id=' . (int) $item->vendor_id . '&client=' . $input->get('client', '', 'STRING')
		. '&extension=' . $input->get('extension', '', 'STRING'), false
		);
		$this->setRedirect($link);
	}

	/**
	 * Function to edit field data
	 *
	 * @param   integer  $key     The primary key id for the item.
	 * @param   string   $urlVar  The name of the URL variable for the id.
	 * 
	 * @return  void
	 */
	public function edit($key = null,$urlVar = null)
	{
		$input    = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) $input->getInt('vendor_id');

		$currency = (STRING) (count($cid) ? $cid[0] : $input->get('currency'));

		$link = JRoute::_(
		'index.php?option=com_tjvendors&view=vendorfee&layout=edit&vendor_id= ' . $recordId . '&currency=' . $currency, false
		);
		$this->setRedirect($link);
	}
}
