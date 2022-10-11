<?php

namespace DL\Component\Multipolls\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

class QuestionController extends FormController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MULTIPOLLS_QUESTIONS';

	/**
	 * Method to run batch operations.
	 *
	 * @param   string  $model  The model
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		$this->checkToken();

		// Set the model
		$model = $this->getModel('Question', '', array());

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_multipolls&view=questions' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
	{
		$item = $model->getItem();

		$rangeModel = $this->getModel('Range', 'Administrator');

		if(in_array($validData['id_type'], [3,5])){
			// сохраняем максимальное значение шкалы
			$rangeModel->setRangeValue($item->get('id'), $validData['max_range']);
		} else {
			// удаляем максимальное значение шкалы
			$rangeModel->deleteRangeValue($item->get('id'));
		}
	}
}
