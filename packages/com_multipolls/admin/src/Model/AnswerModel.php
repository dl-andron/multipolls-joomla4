<?php

namespace DL\Component\Multipolls\Administrator\Model;

use Exception;
use DL\Component\Multipolls\Administrator\Helper\MultipollsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class AnswerModel extends AdminModel
{
	/**
	 * Allowed batch commands
	 *
	 * @var  array
	 */
	protected $batch_commands = array(
		'question_id'   => 'batchQuestion',
	);

	/**
	 * Установленные языки
	 *
	 * @var    array
	 */
	protected $languages = [];

	public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		parent::__construct($config, $factory, $formFactory);

		$this->languages = MultipollsHelper::getPublishedLanguages();
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 */
	public function getForm($data = array(), $loadData = false)
	{
		$app  = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_multipolls.answer', 'answer', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}

		$titlesFieldset = '<fieldset name="titles">';

		// добавляем поля для каждого языка
		foreach($this->languages as $language){
			$labelName = Text::sprintf('COM_MULTIPOLLS_TITLE', $language);
			$titlesFieldset .= '<field name="name_' . $language . '" type="text" label="'. $labelName .'" />';
		}

		$titlesFieldset .= '</fieldset>';

		$titlesElement = new \SimpleXMLElement($titlesFieldset);
		$form->setField($titlesElement);

		$record = new \stdClass();
		$id = (int) $this->getState('answer.id', $app->input->getInt('id', 0));
		$record->id = $id;

		if (!$this->canEditState($record)) {
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		$data = $this->loadFormData();

		$form->bind($data);

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_multipolls.edit.answer.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  bool|Table  A Table object
	 *
	 * @since   4.0.0

	 * @throws  Exception
	 */
	public function getTable( $type = 'answers', $prefix = '', $config = array( ) ) {
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Пакетное редактирование вопроса для группы ответов.
	 *
	 * @param   string  $value     The new value matching a client.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	protected function batchQuestion($value, $pks, $contexts)
	{
		// Set the variables
		$user = Factory::getUser();

		/** @var \DL\Component\Multipolls\Administrator\Table\AnswersTable $table */
		$table = $this->getTable();

		foreach ($pks as $pk) {
			if (!$user->authorise('core.edit', $contexts[$pk])) {
				$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

				return false;
			}

			$table->reset();
			$table->load($pk);
			$table->id_question = (int) $value;

			if (!$table->store()) {
				$this->setError($table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   \Joomla\CMS\Table\Table  $table  A Table object.
	 *
	 * @return  void
	 *
	 */
	protected function prepareTable($table)
	{
		if ((int) $table->published == 0) {
			$table->published = 0;
		}

		// Set the publish date to now
		if ((int) $table->created == 0) {
			$table->created = Factory::getDate('now', 'Europe/Minsk')->toSql();
		}

		// Set the publish date to now
		if ((int) $table->publish_up == 0) {
			$table->publish_up = $table->created;
		}

		if (intval($table->publish_down) == 0) {
			$table->publish_down = null;
		}
	}

	/**
	 * Удаляет ответы если удален вопрос
	 *
	 * @param $pks array Id вопросов
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function postQuestionsDeleteHook($pks)
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id')))
			->from($db->quoteName('#__multipolls_answers'))
			->whereIn($query->quoteName('id_question'), $pks);

		$db->setQuery($query);
		$answers = $db->loadColumn();

		// очищаем результаты
		/** @var \DL\Component\Multipolls\Administrator\Model\StatModel $model */
		$statModel = $this->getMVCFactory()->createModel('Stat', 'Administrator', ['ignore_request' => true]);
		$statModel->clearQuestionsResults($pks);

		return parent::delete($answers);
	}
}
