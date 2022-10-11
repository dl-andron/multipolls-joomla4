<?php

namespace DL\Component\Multipolls\Administrator\View\Answers;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  1.6
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  1.6
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  \Joomla\CMS\Form\Form
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * Is this view an Empty State
	 *
	 * @var  boolean
	 * @since 4.0.0
	 */
	private $isEmptyState = false;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
			$this->setLayout('emptystate');
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
	 */
	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_multipolls');
		$user  = Factory::getApplication()->getIdentity();

		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_MULTIPOLLS') .': '. Text::_('COM_MULTIPOLLS_ANSWERS'), 'copy');

		if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_multipolls', 'core.create')) > 0) {
			$toolbar->addNew('answer.add');
		}

		if (!$this->isEmptyState && $canDo->get('core.edit.state')) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canDo->get('core.edit.state')) {
				$childBar->publish('answers.publish')->listCheck(true);

				$childBar->unpublish('answers.unpublish')->listCheck(true);
			}

			// Add a batch button
			if ($canDo->get('core.edit')) {
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}
		}

		if (!$this->isEmptyState && $canDo->get('core.delete')) {
			$toolbar->delete('answers.delete')
				->text('JTOOLBAR_DELETE')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		if ($user->authorise('core.admin', 'com_multipolls') || $user->authorise('core.options', 'com_multipolls')) {
			$toolbar->preferences('com_multipolls');
		}
	}
}
