<?php

namespace DL\Component\Multipolls\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class PollController extends FormController
{
	/**
	 * Показывает результаты опроса
	 *
	 * @return false|void
	 *
	 */
	public function showResults()
	{
		$id = (int) $this->input->get->get('id', 0, 'int');

		$this->setRedirect(
			Route::_(
			'index.php?option=' . $this->option . '&view=' . $this->view_item
			. $this->getRedirectToItemAppend($id),
			false
			)
		);
	}

	/**
	 * Очищает результаты опроса
	 *
	 * @return false|void
	 *
	 */
	public function clearResults()
	{
		// Access check.
		if (!$this->allowEdit()) {
			$this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');

			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(),
					false
				)
			);

			return false;
		}

		$id = (int) $this->input->get->get('id', 0, 'int');

		// Set the model
		$model = $this->getModel('Stat', '', array());

		// Attempt to run the batch operation.
		if ($model->clearResults($id)) {
			$this->setMessage(Text::_('COM_MULTIPOLLS_STAT_CLEARED'));
		} else {
			$this->setMessage(Text::sprintf('COM_MULTIPOLLS_CLEAR_STAT_ERROR', $model->getError()), 'warning');
		}

		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list .
			$this->getRedirectToListAppend(),
			false));
	}
}
