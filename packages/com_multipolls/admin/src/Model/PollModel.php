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

\defined('_JEXEC') or die;

class PollModel extends AdminModel
{
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
		$form = $this->loadForm('com_multipolls.poll', 'poll', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}

		// добавляем поля для каждого языка
		foreach($this->languages as $language){
			// Add new fields to a new fieldset.
			$xml = '<fieldset name="details-'. $language .'">';

			$xml .= '<field name="name_' . $language . '" type="text" label="JGLOBAL_TITLE" />';
			$xml .= '<field name="text_' . $language . '" type="editor" label="JGLOBAL_DESCRIPTION" filter="JComponentHelper::filterText"
				buttons="true" hide="readmore,pagebreak,module,article,contact,menu"/>';

			$xml .= '</fieldset>';

			// Add new fieldset to form.
			$element = new \SimpleXMLElement($xml);
			$form->setField($element);
		}

        $record = new \stdClass();
		$id = (int) $this->getState('poll.id', $app->input->getInt('id', 0));
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
		$data = $app->getUserState('com_multipolls.edit.poll.data', array());

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
	public function getTable( $type = 'polls', $prefix = '', $config = array( ) ) {
		return parent::getTable($type, $prefix, $config);
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
	 * Получает результаты
	 *
	 * @return object
	 *
	 * @throws Exception
	 * @since version
	 */
	public function getStat()
	{
		$app  = Factory::getApplication();

		$id = $this->getState('poll.id', $app->input->getInt('id', 0));

		/** @var \DL\Component\Multipolls\Administrator\Model\StatModel $model */
		$statModel = $this->getMVCFactory()->createModel('Stat', 'Administrator', ['ignore_request' => true]);

		return $statModel->getStat($id);
	}
}
