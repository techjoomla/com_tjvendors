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

jimport('joomla.application.component.controller');

/**
 * Class TjvendorsController
 *
 * @since  1.6
 */
class TjvendorsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = JFactory::getApplication()->input->getCmd('view', 'vendors');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	/**
	 * Get payment gateway html from plugin
	 *
	 * @return  html
	 *
	 * @since   1.5
	 */
	public function pregetHTML()
	{
		$jinput    = JFactory::getApplication()->input;
		$client = $jinput->get('client');

		$params               = JComponentHelper::getParams($client);
		$gateways             = $params->get('gateways');

		JPluginHelper::importPlugin('payment');
		$dispatcher = JDispatcher::getInstance();
		$html       = $dispatcher->trigger('PreonTP_GetHTML', array($gateways));

		return $html;
	}
}
