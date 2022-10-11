<?php

namespace DL\Component\Multipolls\Administrator\View\Poll;

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
		if ($this->getLayout() == 'show') {
			$this->result  = $this->get('Stat');
		} else {
			$this->form  = $this->get('Form');
			$this->item  = $this->get('Item');
			$this->state = $this->get('State');

			$model       = $this->getModel();
			$this->langs = $model->languages;
		}

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
		$layout = $this->getLayout();

		if($layout == 'show'){
			ToolbarHelper::title(Text::_('COM_MULTIPOLLS_POLL_STAT'),'copy');
		} else {
			ToolbarHelper::title(
				$isNew ? Text::_('COM_MULTIPOLLS_POLL_NEW') : Text::_('COM_MULTIPOLLS_POLL_EDIT'),
				'copy'
			);
		}

		if($layout != 'show'){
			$toolbarButtons = [];

			// If not checked out, can save the item.
			if ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_multipolls', 'core.create')) > 0) {
				ToolbarHelper::apply('poll.apply');
				$toolbarButtons[] = ['save', 'poll.save'];

				if ($canDo->get('core.create')) {
					$toolbarButtons[] = ['save2new', 'poll.save2new'];
				}
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);
		}

		if($layout == 'show' || !empty($this->item->id)){
			ToolbarHelper::cancel('poll.cancel', 'JTOOLBAR_CLOSE');
		} else {
			ToolbarHelper::cancel('poll.cancel');
		}
	}
}
