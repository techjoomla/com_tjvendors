<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Vendor view class
 *
 * @since  1.0.0
 */
class TjvendorsViewVendor extends HtmlView
{
	/**
	 * Object of the vendor class
	 *
	 * @var    \Joomla\CMS\Form\Form;
	 * @since  __DEPLOY_VERSION__
	 */
	protected $form;

	/**
	 * Object of the vendor class
	 *
	 * @var    TjvendorsVendor
	 * @since  __DEPLOY_VERSION__
	 */
	protected $vendor;

	/**
	 * The current client of the vendor
	 * eg. com_jticketing, com_jgive
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $client;

	/**
	 * params of the component
	 *
	 * @var    Joomla\Registry\Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$this->params = TJVendors::config();
		$this->form   = $this->get('Form');
		$input  = Factory::getApplication()->input;
		$this->client = $input->get('client', '', 'STRING');
		$layout = $input->get('layout', '', 'STRING');
		$vendorId = $input->getInt('vendor_id');
		$this->vendor = TJVendors::vendor()->loadByUserId();

		$user = Factory::getUser();

		if ($user->guest)
		{
			$return = base64_encode(Uri::getInstance());
			$loginUrlWithReturn = Route::_('index.php?option=com_users&view=login&return=' . $return);
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'notice');
			$app->redirect($loginUrlWithReturn, 403);
		}
		else
		{
			if ($layout == 'profile' && ($this->vendor->vendor_id !== $vendorId || !$user->authorise('core.edit.own')))
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'notice');
				$app->redirect(Route::_('index.php'), 403);
			}
			elseif ($layout == 'edit')
			{
				// Do nothing
			}
			else
			{
				// Change to vendor provided in the url
				if (!empty($vendorId))
				{
					$this->vendor = TJVendors::vendor($vendorId);
				}

				if (empty($this->vendor->vendor_id))
				{
					$app->enqueueMessage(Text::_('The vendor is not exist'), 'notice');
					$app->redirect(Route::_('index.php'), 403);
				}
			}
		}

		Text::script('COM_TJVENDOR_PAYMENTGATEWAY_NO_FIELD_MESSAGE');
		Text::script('COM_TJVENDOR_DESCRIPTION_READ_MORE');
		Text::script('COM_TJVENDOR_DESCRIPTION_READ_LESS');

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
