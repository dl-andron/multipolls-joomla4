<?php

namespace DL\Component\Multipolls\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class StatModel extends BaseDatabaseModel
{
	private $lang_tag;

	public function __construct($config = array())
	{
		parent::__construct( $config );
		$this->lang_tag = Factory::getLanguage()->getTag();
	}

	/**
	 * Очищает результаты опроса
	 *
	 * @param   integer  $id_poll  ID опроса
	 *
	 * @return  boolean  True on success, false on failure
	 */
	public function clearResults($id_poll)
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id')))
			->from($db->quoteName('#__multipolls_questions'))
			->where($db->quoteName('id_poll') . ' = :id_poll')
			->bind(':id_poll', $id_poll, ParameterType::INTEGER);

		$db->setQuery($query);
		$questions = $db->loadColumn();

		if(!empty($questions) && !$this->clearQuestionsResults($questions)){
			$this->setError($this->getError());
			return false;
		}

		return true;
	}

	/**
	 * Очищает ответы
	 *
	 * @param $pks
	 *
	 * @return false|void
	 *
	 * @since version
	 */
	public function clearQuestionsResults($pks)
	{
		$db = $this->getDatabase();

		$db->transactionStart();

		try {

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_radio_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_cb_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_select_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_text_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_select_text_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_radio_own_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_yn_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_cb_own_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true);

			$query->delete($query->quoteName('#__multipolls_priority_votes'))
				->whereIn($query->quoteName('id_question'), $pks);

			$db->setQuery($query)->execute();

		} catch (\Exception $e) {
			$db->transactionRollback();
			$this->setError($e->getMessage());

			return false;
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * Получает результаты опроса
	 *
	 * @param $id_poll integer Id опроса
	 *
	 * @return object
	 */
	public function getStat($id_poll)
	{
		$db = $this->getDatabase();

		$result = new \stdClass;

		//получаю название опроса
		$query = $db->getQuery(true);

		$query->select($db->quoteName('name_'. $this->lang_tag))
	        ->from($db->quoteName('#__multipolls_polls'))
			->where($db->quoteName('id') . ' = :id_poll')
			->bind(':id_poll', $id_poll, ParameterType::INTEGER);

		$db->setQuery($query);

		$result->poll_name = $db->loadResult();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('name_'. $this->lang_tag, 'name'))
			->select($db->quoteName(['id', 'id_type']))
			->from($db->quoteName('#__multipolls_questions'))
			->where($db->quoteName('id_poll') . ' = :id_poll')
			->where($db->quoteName('published') . ' = 1')
			->order('ordering, id')
			->bind(':id_poll', $id_poll, ParameterType::INTEGER);;

		$db->setQuery($query);

		$questions = $db->loadObjectList();

		foreach($questions as $question){

			$result->questions[$question->id]['name'] = $question->name;
			$result->questions[$question->id]['type'] = $question->id_type;

			switch ($question->id_type)
			{
				case '1':
					$stat = $this->getRadioVotes($question->id);
					break;

				case '2':
					$stat = $this->getCbVotes($question->id);
					break;

				case '3':
					$stat = $this->getSelectVotes($question->id);
					break;

				case '4':
					$stat = $this->getTextVotes($question->id);
					break;

				case '5':
					$stat = $this->getSelectTextVotes($question->id);
					break;

				case '6':
					$stat = $this->getRadioOwnVotes($question->id);
					break;

				case '7':
					$stat = $this->getYnVotes($question->id);
					break;

				case '8':
					$stat = $this->getCbOwnVotes($question->id);
					break;

				case '9':
					$stat = $this->getPriorityVotes($question->id);
					break;

				default:
					break;
			}

			$result->questions[$question->id]['votes'] = $stat;
		}

		return $result;
	}

	/**
	 * Получает результаты для типа "Один вариант"
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getRadioVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('id_answer'))
			->from($db->quoteName('#__multipolls_radio_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadColumn();

		$counts = array_count_values($votes);

		foreach ($answers as $key => $value){
			if(isset($counts[$value->id])){
				$answers[$key]->count = $counts[$value->id];
			} else {
				$answers[$key]->count = 0;
			}
		}

		return $answers;
	}

	/**
	 * Получает результаты для типа "Несколько вариантов".
	 * Если пользователь выбрал хотя бы один чекбокс в вопросе
	 * это считается за одно участие в этом вопросе
	 * результат одного чекбокса: это сколько раз выбрали чекбокс
	 * относительно общего количества участий в вопросе в процентах
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getCbVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('answers'))
			->from($db->quoteName('#__multipolls_cb_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadColumn();

		if(!empty($votes)){
			foreach ($votes as $key => $vote){
				$votes[$key] = explode(',' , $vote);
			}

			foreach ($answers as $key => $value){
				$sum = 0;

				foreach ($votes as $vote){
					if(in_array($value->id, $vote)){
						$sum ++;
					}
				}

				$answers[$key]->count = $sum;
			}
		} else {
			foreach ($answers as $key => $value){
				$answers[$key]->count = 0;
			}
		}

		$result = new \StdClass;

		$result->votes = $votes;
		$result->names = $answers;

		return $result;
	}

	/**
	 * Получает результаты для типа "Цифра по шкале".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getSelectVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		foreach ($answers as $key => $answer) {

			$query = $db->getQuery(true);

			$query->select($db->quoteName('value'))
				->from($db->quoteName('#__multipolls_select_votes'))
				->where($db->quoteName('id_answer') . ' = :id_answer')
				->bind(':id_answer', $answer->id, ParameterType::INTEGER);

			$db->setQuery($query);
			$votes  = $db->loadColumn();

			$counts = array_count_values($votes);
			ksort($counts);

			if ($votes)	{
				$answers[$key]->counts = $counts;
			} else {
				$answers[$key]->counts = [];
			}
		}

		return $answers;
	}

	/**
	 * Получает результаты для типа "Ввод текста".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getTextVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('text'))
			->select($db->quoteName('id_answer'))
			->from($db->quoteName('#__multipolls_text_votes', 'v'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadAssocList();

		$arrIdAnswerText = [];

		// привожу к виду array([id_answer1] => array(text1,text2,text3...) [id_answer2] => array(text3,text4,text5...))
		foreach($votes as $vote){
			$arrIdAnswerText[$vote['id_answer']][] = $vote['text'];
		}

		foreach ($answers as $key => $answer){
			foreach ($arrIdAnswerText as $key_answer => $votes){
				$counts = array_count_values($votes);
				arsort($counts);
				if($answer->id == $key_answer){
					$answers[$key]->answers = $counts;
				}
			}
		}

		return $answers;
	}

	/**
	 * Получает результаты для типа "Цифра по шкале и ввод текста".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getSelectTextVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		foreach ($answers as $key => $answer) {

			$query = $db->getQuery(true);
			$query->select($db->quoteName('value'))
				->from($db->quoteName('#__multipolls_select_text_votes'))
				->where($db->quoteName('id_answer') . ' = :id_answer')
				->bind(':id_answer', $answer->id, ParameterType::INTEGER);

			$db->setQuery($query);
			$votes  = $db->loadColumn();

			$counts = array_count_values($votes);
			ksort($counts);

			if ($votes)	{
				$answers[$key]->counts = $counts;
			} else {
				$answers[$key]->counts = [];
			}
		}

		$query = $db->getQuery(true);

		$query->select($db->quoteName(['text', 'id_answer']))
			->from($db->quoteName('#__multipolls_select_text_votes', 'v'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes  = $db->loadAssocList();

		$arrIdAnswerText = [];

		//привожу к виду array([id_answer1] => array(text1,text2,text3...) [id_answer2] => array(text3,text4,text5...))
		foreach($votes as $vote) {
			if(trim($vote['text']) != ''){
				$arrIdAnswerText[$vote['id_answer']][] = $vote['text'];
			}
		}

		foreach ($answers as $key => $answer){
			foreach ($arrIdAnswerText as $key_answer => $votes){
				$counts = array_count_values($votes);
				arsort($counts);
				if($answer->id == $key_answer){
					$answers[$key]->answers = $counts;
				}
			}
		}

		return $answers;
	}

	/**
	 * Получает результаты для типа "Один вариант либо свой".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getRadioOwnVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('id_answer'))
			->from($db->quoteName('#__multipolls_radio_own_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadColumn();

		$counts = array_count_values($votes);

		foreach ($answers as $key => $answer) {
			if(isset($counts[$answer->id])){
				$answers[$key]->count = $counts[$answer->id];
			} else {
				$answers[$key]->count = 0;
			}
		}

		$result = new \stdClass();

		$result->votes = $answers;

		$query = $db->getQuery(true);

		$query->select($db->quoteName('own_answer'))
			->from($db->quoteName('#__multipolls_radio_own_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->where($db->quoteName('own_answer') . ' <> ' . $db->quote(''))
			->order('id_vote')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$textVotes = $db->loadColumn();

		$result->textVotes = $textVotes;

		return $result;
	}

	/**
	 * Получает результаты для типа "Да или нет".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getYnVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select("concat(id_answer,'_', value) as id_uniq")
			->select($db->quoteName('id_answer'))
			->select($db->quoteName('value'))
			->select('COUNT(*) as cnt')
			->from($db->quoteName('#__multipolls_yn_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->group('id_answer, value')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadObjectList('id_uniq');

		$arVar = array('y','n');

		foreach ($answers as $key => $answer)
		{
			foreach ($arVar as $var)
			{
				$id_uniq = $answer->id. '_'. $var;
				if(isset($votes[$id_uniq]->cnt)){
					$answers[$key]->{$var} = $votes[$id_uniq]->cnt;
				} else {
					$answers[$key]->{$var} = 0;
				}
			}
		}

		return $answers;
	}

	/**
	 * Получает результаты для типа "Несколько вариантов и свой".
	 * Если пользователь выбрал хотя бы один чекбокс в вопросе
	 * это считается за одно участие в этом вопросе
	 * результат одного чекбокса: это сколько раз выбрали чекбокс
	 * относительно общего количества участий в вопросе в процентах
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getCbOwnVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('answers'))
			->from($db->quoteName('#__multipolls_cb_own_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->where($db->quoteName('own_answer') . ' = ' . $db->quote(''))
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$votes = $db->loadColumn();

		if(!empty($votes)){
			foreach ($votes as $key => $vote){
				$votes[$key] = explode(',' , $vote);
			}

			foreach ($answers as $key => $answer){
				$sum = 0;

				foreach ($votes as $vote){
					if(in_array($answer->id, $vote)){
						$sum ++;
					}
				}

				$answers[$key]->count = $sum;
			}
		} else {
			foreach ($answers as $key => $answer){
				$answers[$key]->count = 0;
			}
		}

		$result = new \StdClass;

		$result->votes = $votes;
		$result->names = $answers;

		$query = $db->getQuery(true);

		$query->select($db->quoteName('own_answer'))
			->from($db->quoteName('#__multipolls_cb_own_votes'))
			->where($db->quoteName('id_question') . ' = :id_question')
			->where($db->quoteName('own_answer') . ' <> ' . $db->quote(''))
			->order('id_vote')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);
		$textVotes = $db->loadColumn();

		$result->textVotes = $textVotes;

		return $result;
	}

	/**
	 * Получает результаты для типа "Выбор по приоритету".
	 *
	 * @param $id_question  integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getPriorityVotes($id_question)
	{
		$answers = $this->getAnswers($id_question);

		$db = $this->getDatabase();

		foreach ($answers as $key => $answer) {

			$query = $db->getQuery(true);

			$query->select($db->quoteName('value'))
				->from($db->quoteName('#__multipolls_priority_votes'))
				->where($db->quoteName('id_answer') . ' = :id_answer')
				->bind(':id_answer', $answer->id, ParameterType::INTEGER);

			$db->setQuery($query);
			$votes  = $db->loadColumn();

			$counts = array_count_values($votes);
			ksort($counts);

			if ($votes)	{
				$answers[$key]->counts = $counts;
			} else {
				$answers[$key]->counts = [];
			}
		}

		return $answers;
	}

	/**
	 * Получает ответы для вопроса
	 *
	 * @param $id_question integer Id вопроса
	 *
	 * @return object
	 *
	 */
	private function getAnswers($id_question)
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true);
		$query->select($db->quoteName('name_'. $this->lang_tag, 'name'))
			->select($db->quoteName('id'))
			->from($db->quoteName('#__multipolls_answers'))
			->where($db->quoteName('id_question')  . ' = :id_question')
			->order('ordering, id')
			->bind(':id_question', $id_question, ParameterType::INTEGER);

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
