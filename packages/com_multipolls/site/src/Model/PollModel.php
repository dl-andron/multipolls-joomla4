<?php

namespace DL\Component\Multipolls\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Language\Text;

class PollModel extends ItemModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('poll.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('form.data', $app->getUserState('com_multipolls.poll.data.'. $pk));
	}

	/**
	 * Проверяет куку, означающую участие пользователя в опросе
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function getCookie()
	{
		$app = Factory::getApplication();

		$pk = $app->input->getInt('id');

		return $app->input->get->cookie->get('multipoll'.$pk, '');
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the poll.
	 *
	 * @return  object|boolean  Menu item data object on success, boolean false
	 */
	public function getItem($pk = null)
	{
		$pk = (int) ($pk ?: $this->getState('poll.id'));

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try{

				$lang_tag = Factory::getLanguage()->getTag();

				$db = $this->getDatabase();
				$query = $db->getQuery(true);

				$query->select(
					$this->getState(
						'item.select',
						[
							$db->quoteName('id'),
							$db->quoteName('name_'.$lang_tag, 'name'),
							$db->quoteName('text_'.$lang_tag, 'text')
						]
					)
				)
					->from($db->quoteName('#__multipolls_polls'))
					->where(
						[
							$db->quoteName('id') . ' = :pk',
							$db->quoteName('published') . ' > 0',
						]
					)
					->bind(':pk', $pk, ParameterType::INTEGER);

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data)) {
					throw new \Exception(Text::_('COM_MULTIPOLLS_POLL_NOT_FOUND'), 404);
				}

				if($this->getState('params')){
					$data->params = clone $this->getState('params');
				}

				$query = $db->getQuery(true);
				$query->select($db->quoteName('id', 'qid'))
					->select($db->quoteName('img_url', 'image'))
					->select($db->quoteName('id_type'))
					->select($db->quoteName('required'))
					->select($db->quoteName('name_own_'.$lang_tag, 'name_own'))
					->select($db->quoteName('name_'.$lang_tag, 'name'))
					->from($db->quoteName('#__multipolls_questions'))
					->where(
						[
							$db->quoteName('id_poll') . ' = :pk',
							$db->quoteName('published') . ' > 0',
						]
					)
					->order('ordering')
					->bind(':pk', $pk, ParameterType::INTEGER);

				$db->setQuery($query);
				$data->questions = $db->loadObjectList();

				foreach ($data->questions as $key => $question){
					$query = $db->getQuery(true)
						->select($db->quoteName('img_url'))
						->select($db->quoteName('id'))
						->select($db->quoteName('name_'.$lang_tag, 'name'))
						->select($db->quoteName('id_question'))
						->from($db->quoteName('#__multipolls_answers'))
						->where(
							[
								$db->quoteName('id_question') . ' = :id_question',
								$db->quoteName('published') . ' > 0',
							]
						)
						->order('ordering')
						->bind(':id_question', $question->qid, ParameterType::INTEGER);

					$db->setQuery($query);
					$data->questions[$key]->answers = $db->loadObjectList();
				}

				$this->_item[$pk] = $data;

			} catch (\Exception $e) {
				if ($e->getCode() == 404) {
					// Need to go through the error handler to allow Redirect to work.
					throw $e;
				} else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	public function getRangeValue($id_question)
	{
		/** @var \DL\Component\Multipolls\Administrator\Model\RangeModel $rangeModel */
		$rangeModel = $this->getMVCFactory()->createModel('Range', 'Administrator', ['ignore_request' => true]);

		$rangeRow = $rangeModel->getRangeValue($id_question);

		return $rangeRow['max_range'];
	}

	/**
	 * Исключает ответы не касающиеся выбранного опроса
	 *
	 * @param $id integer id опроса
	 * @param $votes array Результаты опроса
	 *
	 * @return array|false
	 *
	 */
	public function filter($id, $votes)
	{
		try{
			$db = $this->getDatabase();

			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'))
				->from($db->quoteName('#__multipolls_questions'))
				->where($db->quoteName('id_poll') . ' = :id_poll')
				->where($db->quoteName('published') . ' = 1')
				->bind(':id_poll', $id);
			$db->setQuery($query);

			$id_questions = $db->loadColumn();
		} catch (\Exception $e) {
			$this->setError($e->getMessage());

			return false;
		}

		foreach($votes as $type => $values){
			switch ($type) {
				case "r":
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['r'][$key]);
						}
					}
					break;
				case "cb":
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['cb'][$key]);
						}
					}
					break;
				case "s":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['s'][$key]);
						}
					}
					break;
				case "ta":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['ta'][$key]);
						}
					}
					break;
				case "sta":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['sta'][$key]);
						}
					}
					break;
				case "sta-text":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['sta-text'][$key]);
						}
					}
					break;
				case "ro":
					foreach($values as $key => $value){
						$question = explode('-', $key);
						if(!empty($question[1])){
							$id_question = $question[1];
						} else {
							$id_question = $question[0];
						}
						if(!in_array($id_question, $id_questions)){
							unset($votes['ro'][$key]);
							unset($votes['ro']['custom-'.$key]);
						}
					}
					break;
				case "yn":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['yn'][$key]);
						}
					}
					break;
				case "cbo":
					foreach($values as $key => $value){
						$question = explode('-', $key);
						if(!empty($question[1])){
							$id_question = $question[1];
						} else {
							$id_question = $question[0];
						}
						if(!in_array($id_question, $id_questions)){
							unset($votes['cbo'][$key]);
							unset($votes['cbo']['custom-'.$key]);
						}
					}
					break;
				case "priority":
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['priority'][$key]);
						}
					}
					break;
				default:
					break;
			}
		}

		return $votes;
	}

	/**
	 * Проверяет наличие обязательных ответов
	 *
	 * @param $id integer id опроса
	 * @param $votes array Результаты опроса
	 *
	 * @return boolean
	 */
	public function validate($id, $votes)
	{
		// получаю вопросы по выбранному опросу в виде массива
		// Array ( [id_question] => Array ( [id_question] => 1 [required] => 0 [type] => r
		// [answers] => Array ( [0] => id_answer1 [1] => id_answer2) ...)

		try{
			$db = $this->getDatabase();

			$query = $db->getQuery(true);

			$query->select($db->quoteName(['id', 'required']));
			$query->select("CASE".
				" WHEN " . $db->quoteName('id_type') . " = 1 THEN 'r'".
				" WHEN " . $db->quoteName('id_type') . " = 2 THEN 'cb'".
				" WHEN " . $db->quoteName('id_type') . " = 3 THEN 's'".
				" WHEN " . $db->quoteName('id_type') . " = 4 THEN 'ta'".
				" WHEN " . $db->quoteName('id_type') . " = 5 THEN 'sta'".
				" WHEN " . $db->quoteName('id_type') . " = 6 THEN 'ro'".
				" WHEN " . $db->quoteName('id_type') . " = 7 THEN 'yn'".
				" WHEN " . $db->quoteName('id_type') . " = 8 THEN 'cbo'".
				" WHEN " . $db->quoteName('id_type') . " = 9 THEN 'priority'".
				" END AS " . $db->quoteName('type'))

				->from($db->quoteName('#__multipolls_questions'))
				->where($db->quoteName('id_poll') . ' = :id_poll')
				->where($db->quoteName('published') . ' = 1')
				->bind(':id_poll', $id);

			$db->setQuery($query);

			$questions = $db->loadObjectList('id');
		} catch (\Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		foreach($questions as $key => $question){
			try {
				$query = $db->getQuery(true);

				$query->select($db->quoteName('id'))
					->from($db->quoteName('#__multipolls_answers'))
					->where($db->quoteName('id_question') . ' = :id_question')
					->where($db->quoteName('published') . ' = 1')
					->bind(':id_question', $question->id);

				$db->setQuery($query);

				$questions[$key]->answers = $db->loadColumn();
			} catch (\Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}

		foreach($questions as $id_question => $question){
			switch ($question->type) {
				case "r":
					if($question->required &&
						(
							empty($votes['r'][$id_question]) ||
							(!empty($votes['r'][$id_question]) && !in_array($votes['r'][$id_question], $question->answers))
						)
					){
						$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
						return false;
					}
					break;
				case "cb":
					if($question->required &&
						(
							empty($votes['cb'][$id_question]) ||
							(!empty($votes['cb'][$id_question]) && count(array_intersect($question->answers, $votes['cb'][$id_question])) == 0)
						)
					){
						$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
						return false;
					}
					break;
				case "s":
					if($question->required) {
						foreach($question->answers as $id_answer){
							if(empty($votes['s'][$id_question.'-'.$id_answer])){
								$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
								return false;
							}
						}
					}
					break;
				case "ta":
					if($question->required) {
						foreach($question->answers as $id_answer){
							if(empty(trim($votes['ta'][$id_question.'-'.$id_answer]))){
								$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
								return false;
							}
						}
					}
					break;
				case "sta":
					if($question->required) {
						foreach($question->answers as $id_answer){
							if(empty($votes['sta'][$id_question.'-'.$id_answer]) || empty(trim($votes['sta-text'][$id_question.'-'.$id_answer]))){
								$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
								return false;
							}
						}
					}
					break;
				case "ro":
					if($question->required &&
						(
							empty($votes['ro'][$id_question]) ||
							(
								!empty($votes['ro'][$id_question]) && $votes['ro'][$id_question] != 'custom' &&
								!in_array($votes['ro'][$id_question], $question->answers)
							) ||
							(
								$votes['ro'][$id_question] == 'custom' &&
								empty(trim($votes['ro']['custom-'.$id_question]))
							)
						)
					){
						$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
						return false;
					}
					break;
				case "yn":
					if($question->required) {
						foreach($question->answers as $id_answer){
							if(empty($votes['yn'][$id_question.'-'.$id_answer])){
								$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
								return false;
							}
						}
					}
					break;
				case "cbo":
					if($question->required &&
						(
							empty($votes['cbo'][$id_question]) ||
							(
								!empty($votes['cbo'][$id_question]) &&
								!in_array('custom', $votes['cbo'][$id_question]) &&
								count(array_intersect($question->answers, $votes['cbo'][$id_question])) == 0
							) ||
							(in_array('custom', $votes['cbo'][$id_question]) && empty(trim($votes['cbo']['custom-'.$id_question])))
						)
					){
						$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
						return false;
					}
					break;
				case "priority":
					if($question->required) {
						foreach($question->answers as $id_answer){
							if(!in_array($id_answer, $votes['priority'][$id_question])){
								$this->setError(Text::_('COM_MULTIPOLLS_VOTES_ERROR'));
								return false;
							}
						}
					}
					break;
				default:
					break;
			}
		}

		return true;
	}

	/**
	 * @param $data object Данные пользователя
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function save($data)
	{
		$id_user = Factory::getUser()->id;

		$db = $this->getDatabase();

		$db->transactionStart();

		if(!empty($data->votes['r']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_radio_votes'))
				->columns($db->quoteName(['id_question','id_answer', 'id_user', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['r'] as $key => $vote)
			{
				$query->values(implode(',', $query->bindArray(
					[$key, $vote, $id_user, $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
				);
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['cb']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_cb_votes'))
				->columns($db->quoteName(['id_question', 'answers', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['cb'] as $key => $vote)
			{
				$query->values(implode(',', $query->bindArray(
					[$key, implode(',', $vote), $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
				);
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['s']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_select_votes'))
				->columns($db->quoteName(['id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['s'] as $key => $vote)
			{
				// Каждое имя поля ввода ответа состоит из [id_вопроса-id_ответа]
				// вытягиваем id_вопроса

				$id_question = explode("-", $key)[0];

				// проверяем максимально допустимое значение для ответа
				$max_available_val = $this->getRangeValue($id_question);

				if (!is_numeric($vote) || $vote < 1 || $vote > $max_available_val)
				{
					$db->transactionRollback();
					$this->setError(Text::_('COM_MULTIPOLLS_INCORRECT_ANSWER'));
					return false;
				}

				$qa = explode("-", $key);

				$query->values(implode(',', $query->bindArray(
						[$qa[0], $qa[1], $vote, $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
				);
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['ta']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_text_votes'))
				->columns($db->quoteName(['id_question', 'id_answer', 'text', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['ta'] as $key => $vote)
			{
				if(trim($vote) != '')
				{
					$qa = explode("-", $key);

					$query->values(implode(',', $query->bindArray(
							[$qa[0], $qa[1], $vote, $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);
				}
			}

			if($query->values)
			{
				try
				{
					$db->setQuery($query)->execute();
				}
				catch (\Exception $e)
				{
					$db->transactionRollback();
					$this->setError($e->getMessage());
					return false;
				}
			}
		}

		if(!empty($data->votes['sta']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_select_text_votes'))
				->columns($db->quoteName(['id_question', 'id_answer', 'value', 'text', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['sta'] as $key => $vote)
			{
				// Каждое имя поля ввода ответа состоит из [id_вопроса-id_ответа]
				// вытягиваем id_вопроса

				$id_question = explode("-", $key)[0];

				// проверяем максимально допустимое значение для ответа
				$max_available_val = $this->getRangeValue($id_question);

				if (!is_numeric($vote) || $vote < 1 || $vote > $max_available_val)
				{
					$db->transactionRollback();
					$this->setError(Text::_('COM_MULTIPOLLS_INCORRECT_ANSWER'));
					return false;
				}

				$qa = explode("-", $key);
				$query->values(implode(',', $query->bindArray(
						[$qa[0], $qa[1], $vote, $data->votes['sta-text'][$key], $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
				);
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['ro']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_radio_own_votes'))
				->columns($db->quoteName(['id_question','id_answer', 'id_user', 'own_answer' ,'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['ro'] as $key => $vote)
			{
				if(strpos($key, 'custom-') !== false) {
					continue;
				}

				// флаг наличия данных для вставки
				$exist_data_ro = false;

				if($vote == 'custom') {
					$query->values(implode(',', $query->bindArray(
						[$key, '', $id_user, $data->votes['ro']['custom-'.$key], $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);
					$exist_data_ro = true;
				} else {
					$query->values(implode(',', $query->bindArray(
						[$key, $vote, $id_user, '', $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);
					$exist_data_ro = true;
				}
			}
			try
			{
				if($exist_data_ro){
					$db->setQuery($query)->execute();
				}
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['yn']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_yn_votes'))
				->columns($db->quoteName(['id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['yn'] as $key => $vote)
			{
				$qa = explode("-", $key);

				$query->values(implode(',', $query->bindArray(
					[$qa[0], $qa[1], $vote, $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
				);
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['cbo']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_cb_own_votes'))
				->columns($db->quoteName(['id_question', 'answers', 'own_answer', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['cbo'] as $key => $vote)
			{
				if(strpos($key, 'custom-') !== false) {
					continue;
				}

				// флаг наличия данных для вставки
				$exist_data_cbo = false;

				if(in_array('custom', $vote)){
					$query->values(implode(',', $query->bindArray(
						[$key, '', $data->votes['cbo']['custom-'.$key], $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);

					$key_custom = array_search('custom', $vote);
					unset($vote[$key_custom]);

					$exist_data_cbo = true;
				}

				if(!empty($vote)) {
					$query->values(implode(',', $query->bindArray(
						[$key, implode(",", $vote), '', $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);

					$exist_data_cbo = true;
				}
			}

			try
			{
				if($exist_data_cbo){
					$db->setQuery($query)->execute();
				}
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		if(!empty($data->votes['priority']))
		{
			$query = $db->getQuery(true);

			$query->insert($db->quoteName('#__multipolls_priority_votes'))
				->columns($db->quoteName(['id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting']));

			$dataTypes = [
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
			];

			foreach ($data->votes['priority'] as $id_question => $vote) {
				$i = 1; // приоритет по порядку
				foreach($vote as $id_answer){
					$query->values(implode(',', $query->bindArray(
						[$id_question, $id_answer, $i, $data->ip, $data->user_agent, $data->date_vote], $dataTypes))
					);
					$i++;
				}
			}

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;
			}
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * Получает результаты опроса
	 *
	 * @return object
	 *
	 * @since version
	 */
	public function getStat()
	{
		$app = Factory::getApplication();

		/** @var \DL\Component\Multipolls\Administrator\Model\StatModel $statModel */
		$statModel = $this->getMVCFactory()->createModel('Stat', 'Administrator', ['ignore_request' => true]);

		$pk = $app->input->getInt('id');

		return $statModel->getStat($pk);
	}
}
