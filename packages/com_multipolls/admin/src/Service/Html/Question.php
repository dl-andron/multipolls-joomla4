<?php

namespace DL\Component\Multipolls\Administrator\Service\Html;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareTrait;

\defined('_JEXEC') or die;
/**
 * Banner HTML class.
 *
 * @since  2.5
 */
class Question
{
	use DatabaseAwareTrait;

	/**
	 * Display a batch widget for the client selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public function polls()
	{
		$select = array(
			'<select class="form-select" name="batch[poll_id]" id="batch-poll-id">',
				'<option value="">' . Text::_('COM_MULTIPOLLS_BATCH_POLL_NOCHANGE') . '</option>',
				HTMLHelper::_('select.options', static::pollList(), 'value', 'text'),
				'</select>'
		);
		// Create the batch selector to change the client on a selection list.
		return implode(
			"\n",
			array(
				'<label id="batch-poll-lbl" for="batch-poll-id">',
				Text::_('COM_MULTIPOLLS_BATCH_POLL_LABEL'),
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
	public function pollList()
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
			->from($db->quoteName('#__multipolls_polls'))
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