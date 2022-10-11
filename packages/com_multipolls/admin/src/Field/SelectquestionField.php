<?php

namespace DL\Component\Multipolls\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class SelectquestionField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'SelectQuestion';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $layout = 'joomla.form.field.list-fancy-select';

	/**
	 * Method to get the list of fonts field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.4
	 */
	protected function getOptions()
	{
		$lang_tag = Factory::getLanguage()->getTag();

		$db = $this->getDatabase();

		$columns = $db->getTableColumns('#__multipolls_questions');

		$options = array();
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_MULTIPOLLS_QUESTION_SELECT'));

		if (array_key_exists('name_'. $lang_tag, $columns)) {
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'))
				->select($db->quoteName('name_'. $lang_tag, 'name'))
				->from($db->quoteName('#__multipolls_questions'));

			$db->setQuery($query);
			$questions = $db->loadObjectList();

			foreach ($questions as $question) {
				$options[] = HTMLHelper::_('select.option', $question->id, $question->name);
			}
		}

		return array_merge(parent::getOptions(), $options);
	}
}
