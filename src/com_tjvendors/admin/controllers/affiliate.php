<?php
/**
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Affiliate controller class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjvendorsControllerAffiliate extends FormController
{
	/**
	 * The view list variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_list;

	/**
	 * The input variable
	 *
	 * @var   Object
	 * @since  __DEPLOY_VERSION__
	 */
	protected $input;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'affiliates';
		$this->input = Factory::getApplication()->input;

		parent::__construct();
	}

	/**
	 * Method to save a affiliate data.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	void
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = Factory::getApplication();
		$model = $this->getModel('Affiliate', 'TjvendorsModel');

		// Get the user data.
		$data = $this->input->get('jform', array(), 'array');

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);

		// Validate the posted data.
		$form = $model->getForm();

		if (! $form)
		{
			throw new Exception(implode("\n", $model->getError()));
		}

		// Validate the posted data.
		$validate  = $model->validate($form, $data);

		// Check for errors
		if ($validate === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n; $i ++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			$id = (int) $app->getUserState('com_tjvendors.edit.affiliate.id');
			$app->setUserState('com_tjvendors.edit.affiliate.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(Route::_('index.php?option=com_tjvendors&view=affiliate&layout=edit&id=' . $id, false));

			return false;
		}

		$recordId = $model->save($validate);

		// Check for errors.
		if ($recordId === false)
		{
			// Save the data in the session.
			$app->setUserState('com_tjvendors.edit.affiliate.data', $data);

			$id = $app->getUserState('com_tjvendors.edit.affiliate.data.id');
			$this->setMessage(Text::sprintf('COM_TJVENDORS_AFFILIATE_SAVED_FAILED', $model->getError()), 'warning');
			$dynamicLink = 'id=' . $id;

			$this->setRedirect(
					Route::_(
					'index.php?option=com_tjvendors&view=affiliate&layout=edit&' . $dynamicLink, false
					)
					);

			return false;
		}

		$task = $this->getTask();

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_TJVENDORS_MSG_SUCCESS_SAVE_AFFILIATE'));

		switch ($task)
		{
			case 'apply':
				// Since we want to edit the same record hold the id in the state
				$app->setUserState('com_tjvendors.edit.affiliate.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=affiliate&layout=edit&id=' . $recordId, false));
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjvendors.edit.affiliate', $recordId);
				$app->setUserState('com_tjvendors.edit.affiliate.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_tjvendors&view=affiliate&layout=edit&id=0', false));
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjvendors.edit.affiliate', $recordId);
				$app->setUserState('com_tjvendors.edit.affiliate.data', null);

				$this->setRedirect(Route::_('index.php?option=com_tjvendors&view=affiliates', false));
		}

		return true;
	}
}
