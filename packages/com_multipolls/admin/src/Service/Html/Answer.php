<?php

namespace DL\Component\Multipolls\Administrator\Service\Html;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareTrait;

\defined('_JEXEC') or die;

/**
 * Question HTML class.
 *
 * @since  2.5
 */
class Answer
{
	use DatabaseAwareTrait;

	/**
	 * Display a batch widget for the question selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public function questions()
	{
		$select = array(
			'<select class="form-select" name="batch[question_id]" id="batch-question-id">',
			'<option value="">' . Text::_('COM_MULTIPOLLS_BATCH_QUESTION_NOCHANGE') . '</option>',
			HTMLHelper::_('select.options', static::questionList(), 'value', 'text'),
			'</select>'
		);
		// Create the batch selector to change the client on a selection list.
		return implode(
			"\n",
			array(
				'<label id="batch-question-lbl" for="batch-question-id">',
				Text::_('COM_MULTIPOLLS_BATCH_QUESTION_LABEL'),
				'</label>',
				'<joomla-field-fancy-select>'. implode($select).'</joomla-field-fancy-select>'
			)
		);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	public function questionList()
	{
		$db = $this->getDatabase();
		$lang_tag = Factory::getLanguage()->getTag();

		$query = $db->getQuery(true)
			->select(
				[
					$db->quoteName('id', 'value'),
					$db->quoteName('name_' . $lang_tag, 'text'),
				]
			)
			->from($db->quoteName('#__multipolls_questions'))
			->order($db->quoteName('name_'. $lang_tag));

		// Get the options.
		$db->setQuery($query);

		try {
			$options = $db->loadObjectList();
		} catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $options;
	}

}
