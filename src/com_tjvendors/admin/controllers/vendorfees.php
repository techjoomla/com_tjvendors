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

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Vendors list controller class.
 *
 * @since  1.6
 */
class TjvendorsControllerVendorFees extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'vendorfee', $prefix = 'TjvendorsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method for back to previous page
	 *
	 * @return  boolean
	 */
	public function back()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=vendors', false));
	}

	/**
	 * Method for reseting to commissions
	 *
	 * @return  boolean
	 */
	public function reset()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$cid = $input->post->get('cid', array(), 'array');
		$Id = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$vendorId = $input->post->get('vendor_id', '', 'INT');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendorfee');
		$TjvendorsModelVendor = JModelLegacy::getInstance('Vendorfee', 'TjvendorsModel');
		$vendorDetail = (array) $TjvendorsModelVendor->getItem($Id);
		$model = $this->getModel('vendorfee');
		$result = $model->save($vendorDetail, $reset = 'reset');

		// Redirect to the list screen.
		$link = JRoute::_(
		'index.php?option=com_tjvendors&view=vendorfees&vendor_id=' . $vendorId, false
		);
		$this->setRedirect($link);
	}
}
