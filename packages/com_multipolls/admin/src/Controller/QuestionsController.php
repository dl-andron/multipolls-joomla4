<?php

namespace DL\Component\Multipolls\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Input\Input;

\defined('_JEXEC') or die;

class QuestionsController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MULTIPOLLS_QUESTIONS';

	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The Application for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since   3.0
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
	 *
	 */
	public function getModel($name = 'Question', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   BaseDatabaseModel  $model  The data model object.
	 * @param   integer            $id     The validated data.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function postDeleteHook(BaseDatabaseModel $model, $id = null)
	{
		$answerModel = $this->getModel('Answer', '', array());
		$answerModel->postQuestionsDeleteHook($id);
	}
}