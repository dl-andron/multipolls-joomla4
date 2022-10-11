<?php

namespace DL\Module\Multipolls\Site\Helper;

use DL\Component\Multipolls\Administrator\Model\StatModel;
use DL\Component\Multipolls\Site\Model\PollModel;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class MultipollsHelper
{
	/**
	 * Получает опрос
	 *
	 * @param $id_poll integer Id опроса
	 *
	 * @return bool|object
	 *
	 */
	public static function getPoll($id_poll)
	{
		$app = Factory::getApplication();

		$language = Factory::getLanguage();		
		$extension = 'com_multipolls';
		$language->load($extension);

		/** @var PollModel $model */
		$model = $app->bootComponent('com_multipolls')->getMVCFactory()->createModel('Poll', 'Site', ['ignore_request' => true]);

		return $model->getItem($id_poll);
	}

	/**
	 * Получает наибольшее значение для вопроса типа "Цифра по шкале"
	 *
	 * @param $id_poll integer Id вопроса
	 *
	 * @return integer
	 *
	 */
	public static function getRangeValue($id_question)
	{
		$app = Factory::getApplication();

		/** @var PollModel $model */
		$model = $app->bootComponent('com_multipolls')->getMVCFactory()->createModel('Poll', 'Site', ['ignore_request' => true]);

		return $model->getRangeValue($id_question);
	}

	/**
	 * Получает наибольшее значение для вопроса типа "Цифра по шкале"
	 *
	 * @param $id_poll integer Id опроса
	 *
	 * @return object
	 *
	 */
	public static function getResult($id_poll)
	{
		$app = Factory::getApplication();

		/** @var StatModel $model */
		$model = $app->bootComponent('com_multipolls')->getMVCFactory()->createModel('Stat', 'Administrator', ['ignore_request' => true]);

		return $model->getStat($id_poll);
	}
}
