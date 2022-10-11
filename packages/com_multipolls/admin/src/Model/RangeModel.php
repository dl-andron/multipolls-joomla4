<?php

namespace DL\Component\Multipolls\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

class RangeModel extends BaseDatabaseModel
{
	/**
	 * Получает максимальное значение шкалы для вопроса
	 *
	 * @param   integer  $id_question  ID вопроса
	 *
	 * @return array|null
	 */
	public function getRangeValue($id_question)
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__multipolls_select_range'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 * Сохраняет максимальное значение шкалы для вопроса
	 *
	 * @param   integer  $id_question  ID вопроса
	 *
	 * @return  boolean  True on success, false on failure
	 */
	public function setRangeValue($id_question, $value)
	{
		$exist = $this->getRangeValue($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		if($exist){
			$query->update($db->quoteName('#__multipolls_select_range'))
				->set($db->quoteName('max_range') . ' = :max_range')
				->where($db->quoteName('id') . ' = :pk')
				->bind(':max_range', $value, ParameterType::INTEGER)
				->bind(':pk', $exist['id'], ParameterType::INTEGER);
		} else {
			$query->insert($db->quoteName('#__multipolls_select_range'))
				->columns(
					[
						$db->quoteName('id_question'),
						$db->quoteName('max_range')
					]
				)
				->values(':id_question, :max_range')
				->bind(':id_question', $id_question, ParameterType::INTEGER)
				->bind(':max_range', $value, ParameterType::INTEGER);
		}

		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Удаляет максимальное значение шкалы для вопроса
	 *
	 * @param   integer  $id_question  ID вопроса
	 *
	 * @return  boolean  True on success, false on failure
	 */
	public function deleteRangeValue($id_question)
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->delete('#__multipolls_select_range')
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$db->execute();

		return $db->execute();
	}
}
