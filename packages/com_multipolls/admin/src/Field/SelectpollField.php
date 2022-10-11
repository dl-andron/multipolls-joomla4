<?php

namespace DL\Component\Multipolls\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class SelectpollField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'SelectPoll';

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

		$columns = $db->getTableColumns('#__multipolls_polls');

		$options = array();

		if (array_key_exists('name_'. $lang_tag, $columns)) {
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'))
				->select($db->quoteName('name_'. $lang_tag, 'name'))
				->from($db->quoteName('#__multipolls_polls'));

			$db->setQuery($query);
			$polls = $db->loadObjectList();

			foreach ($polls as $poll) {
				$options[] = HTMLHelper::_('select.option', $poll->id, $poll->name);
			}
		}

		return array_merge(parent::getOptions(), $options);
	}
}
