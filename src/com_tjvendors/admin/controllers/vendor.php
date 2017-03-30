<?php
/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright  2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/tjvendors.php');

/**
 * Vendor controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendor extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'vendors';

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
		$input  = JFactory::getApplication()->input;
		$client = $input->get('client', '', 'STRING');
		$vendor_id = $input->get('vendor_id', '', 'STRING');
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&client=' . $client . '&vendor_id=' . $vendor_id;

		return $append;
	}

	/**
	 * Check for duplicate users
	 * 
	 * @return null
	 * 
	 * @since   1.6
	 */
	public function checkDuplicateUser()
	{
		$input  = JFactory::getApplication()->input->post;
		$user = $input->get('user', '', 'STRING');
		$model = $this->getModel('vendor');
		$results = $model->checkDuplicateUser($user);
		echo json_encode($results);
		jexit();
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
		$input  = JFactory::getApplication()->input->post;
		$client = $input->get('client', '', 'STRING');
		$append = parent::getRedirectToListAppend();
		$append .= '&client=' . $client;

		return $append;
	}
}
