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

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class TjvendorsViewVendorFee extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

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
		$input = JFactory::getApplication()->input;
		$this->foo = $input->get('curr', '', 'ARRAY');
		$currn = $this->foo;
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		
		$this->input = JFactory::getApplication()->input;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		

		$input = JFactory::getApplication()->input;
		$this->full_client = $input->get('client', '', 'STRING');

		// Let's get the extension name
		$client = JFactory::getApplication()->input->get('client', '', 'STRING');
		$extensionName = strtoupper($client);

		if ($isNew)
		{
			$viewTitle = JText::_('COM_TJVENDOR_ADD_USER_SPECIFIC_COMM');
		}
		else
		{
			$viewTitle = JText::_('COM_TJVENDOR_EDIT_USER_SPECIFIC_COMM');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = TjvendorsHelpersTjvendors::getActions();

		if (JVERSION >= '3.0')
		{
			JToolbarHelper::title(JText::_('COM_TJVENDORS_TITLE_VENDOR') . $viewTitle,  'pencil-2');
		}
		else
		{
			JToolbarHelper::title(JText::_('COM_TJVENDORS_TITLE_VENDOR') . $viewTitle, 'course.png');
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('vendorfee.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('vendorfee.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('vendorfee.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolBarHelper::custom('vendorfee.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		// Button for version control
		if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit'))
		{
			JToolbarHelper::versions('com_tjvendors.vendorfee', $this->item->id);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('vendorfee.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('vendorfee.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
