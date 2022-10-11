<?php

namespace DL\Component\Multipolls\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

class AnswerController extends FormController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MULTIPOLLS_ANSWERS';

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
		$model = $this->getModel('Answer', '', array());

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_multipolls&view=answers' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
