<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjvendors
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
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
	public function edit($key = null)
	{
		$input    = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$link = JRoute::_(
		'index.php?option=com_tjvendors&view=vendorfee&layout=edit&id= ' . $recordId . '&client='
		. $input->get('client', '', 'STRING') . '&curr[]=INR&curr[]=USD', false
		);
		$this->setRedirect($link);
	}
}
