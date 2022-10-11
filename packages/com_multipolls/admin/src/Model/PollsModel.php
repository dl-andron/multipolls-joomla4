<?php

namespace DL\Component\Multipolls\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

class PollsModel extends ListModel
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
					$db->quoteName('id'),
					$db->quoteName('created'),
					$db->quoteName('published'),
					$db->quoteName('publish_up'),
					$db->quoteName('publish_down'),
					$db->quoteName('name_'.$lang_tag, 'name')
				]
			)
		);
		$query->from($db->quoteName('#__multipolls_polls'));

		$published = (string) $this->getState('filter.published');

		if (is_numeric($published)) {
			$published = (int) $published;
			$query->where($db->quoteName('published') . ' = :published')
				->bind(':published', $published, ParameterType::INTEGER);
		}

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$search = substr($search, 3);
				$query->where($db->quoteName('id') . ' = :id');
				$query->bind(':id', $search, ParameterType::INTEGER);
			} elseif (stripos($search, 'content:') === 0) {
				$search = '%' . substr($search, 8) . '%';
				$query->where( $db->quoteName('text_'. $lang_tag) . ' LIKE :search')
					->bind([':search'], $search);
			} else {
				$search = '%' . trim($search) . '%';
				$query->where($db->quoteName('name_'. $lang_tag) . ' LIKE :name');
				$query->bind(':name', $search);
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}