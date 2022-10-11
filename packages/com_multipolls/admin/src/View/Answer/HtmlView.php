<?php

namespace DL\Component\Multipolls\Administrator\View\Answer;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var    object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 */
	protected $state;

	/**
	 * Installed languages
	 *
	 * @var    array
	 */
	protected $langs;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null): void
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		$model       = $this->getModel();
		$this->langs = $model->languages;

		// Check for errors.
		if (\count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception
	 */
	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		$canDo = ContentHelper::getActions('com_multipolls');
		$user = $this->getCurrentUser();

		ToolbarHelper::title(
			$isNew ? Text::_('COM_MULTIPOLLS_ANSWER_NEW') : Text::_('COM_MULTIPOLLS_ANSWER_EDIT'),
			'copy'
		);

		$toolbarButtons = [];

		// If not checked out, can save the item.
		if ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_multipolls', 'core.create')) > 0) {
			ToolbarHelper::apply('answer.apply');
			$toolbarButtons[] = ['save', 'answer.save'];

			if ($canDo->get('core.create')) {
				$toolbarButtons[] = ['save2new', 'answer.save2new'];
			}
		}

		ToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		if (empty($this->item->id)) {
			ToolbarHelper::cancel('answer.cancel');
		} else {
			ToolbarHelper::cancel('answer.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
