<?php

namespace DL\Component\Multipolls\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

class AnswersModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name',
				'published',
				'created',
				'id_poll',
				'id_question',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = 'id', $direction = 'desc')
	{
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.id_poll');
		$id .= ':' . $this->getState('filter.id_question');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$lang_tag = Factory::getLanguage()->getTag();

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState('list.select',
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.id_question'),
					$db->quoteName('a.created'),
					$db->quoteName('a.published'),
					$db->quoteName('a.publish_up'),
					$db->quoteName('a.publish_down'),
					$db->quoteName('a.name_'.$lang_tag, 'name'),
					$db->quoteName('q.id_poll'),
					$db->quoteName('q.name_'.$lang_tag, 'question_name'),
					$db->quoteName('p.name_'.$lang_tag, 'poll_name')
				]
			)
		);
		$query->from($db->quoteName('#__multipolls_answers', 'a'));

		$query->join('INNER', $db->quoteName('#__multipolls_questions', 'q'),
			$db->quoteName('a.id_question') . ' = ' . $db->quoteName('q.id'));

		$query->join('INNER', $db->quoteName('#__multipolls_polls', 'p'),
			$db->quoteName('q.id_poll') . ' = ' . $db->quoteName('p.id'));

		$published = (string) $this->getState('filter.published');

		if (is_numeric($published)) {
			$published = (int) $published;
			$query->where($db->quoteName('a.published') . ' = :published')
				->bind(':published', $published, ParameterType::INTEGER);
		}

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$search = substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :id');
				$query->bind(':id', $search, ParameterType::INTEGER);
			} elseif (stripos($search, 'question:') === 0) {
				$search = '%' . substr($search, 9) . '%';
				$query->where( $db->quoteName('q.name_'. $lang_tag) . ' LIKE :search')
					->bind([':search'], $search);
			} elseif (stripos($search, 'poll:') === 0) {
				$search = '%' . substr($search, 5) . '%';
				$query->where( $db->quoteName('p.name_'. $lang_tag) . ' LIKE :search')
					->bind([':search'], $search);
			} else {
				$search = '%' . trim($search) . '%';
				$query->where($db->quoteName('a.name_'. $lang_tag) . ' LIKE :name');
				$query->bind(':name', $search);
			}
		}

		$id_question = $this->getState('filter.id_question');
		if (is_numeric($id_question)) {
			$id_question = (int) $id_question;
			$query->where($db->quoteName('a.id_question') . ' = :id_question')
				->bind(':id_question', $id_question, ParameterType::INTEGER);
		}

		$id_poll = $this->getState('filter.id_poll');
		if (is_numeric($id_poll)) {
			$id_poll = (int) $id_poll;
			$query->where($db->quoteName('q.id_poll') . ' = :id_poll')
				->bind(':id_poll', $id_poll, ParameterType::INTEGER);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
