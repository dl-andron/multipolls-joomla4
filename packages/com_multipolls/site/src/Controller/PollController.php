<?php

namespace DL\Component\Multipolls\Site\Controller;

use DL\Component\Multipolls\Site\Helper\MultipollsHelper;
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class PollController extends BaseController
{
	/**
	 * Получает html результатов для ajax
	 *
	 */
	public function getResults()
	{
		$view = $this->getView('poll', 'html', '', array('base_path' => $this->basePath, 'layout' => 'show'));
		$view->document = $this->app->getDocument();

		if ($model = $this->getModel('poll')) {
			$view->setModel($model, true);
		}

		if ($statModel = $this->getModel('stat', 'Administrator')) {
			$view->setModel($statModel, true);
		}

		$id = $this->input->get('id','', 'int');

		echo $view->renderAjaxResults($id);

		jexit();
	}

	/**
	 * Сохранение результатов опроса
	 *
	 * @return false|void
	 *
	 * @since version
	 */
	public function saveVote()
	{
		// Check for request forgeries.
		$this->checkToken();

		$app = $this->app;

		$redirect = Route::_(base64_decode($this->input->get('return', null, 'base64')), false);

		$user_captcha = trim(strtolower($this->input->get('captcha', '', 'string')));
		$real_captcha = MultipollsHelper::decryptCaptcha($this->input->get('real-captcha', '', 'string'));

		$id = $this->input->get('id','', 'int');
		$requestData = $this->input->get('poll', [], 'ARRAY');

		if ($this->input->get->cookie->get('multipoll'. $id, ''))
		{
			$this->setMessage(Text::_('COM_MULTIPOLLS_ALREADY_VOTED'), 'warning');
			$this->setRedirect($redirect);
			return false;
		}

		// проверка капчи
		if(!$real_captcha || $user_captcha != $real_captcha)
		{
			$app->setUserState('com_multipolls.poll.data.'. $id, $requestData);

			$this->setMessage(Text::_('COM_MULTIPOLLS_CAPTCHA_ERROR'), 'warning');
			$this->setRedirect($redirect);

			return false;
		}

		/** @var \DL\Component\Multipolls\Site\Model\PollModel $model */
		$model = $this->getModel('Poll', 'Site');

		// исключаем ответы не касающиеся выбранного опроса (через код страницы подменили id)
		$filteredData = $model->filter($id, $requestData);

		// проверяем наличие обязательных ответов
		if(!$model->validate($id, $filteredData))
		{
			$app->setUserState('com_multipolls.poll.data.'. $id, $filteredData);

			$this->setMessage(Text::sprintf('COM_MULTIPOLLS_ERROR', $model->getError()), 'warning');
			$this->setRedirect($redirect);

			return false;
		}

		$data = new \stdClass;
		$data->votes = $filteredData;
		$data->ip = $this->input->server->get('REMOTE_ADDR');
		$data->user_agent = Browser::getInstance()->getAgentString();
		$data->date_vote = Factory::getDate('now', 'Europe/Minsk');

		if(!$model->save($data))
		{
			$app->setUserState('com_multipolls.poll.data.'. $id, $filteredData);

			$this->setMessage(Text::sprintf('COM_MULTIPOLLS_ERROR', $model->getError()), 'warning');
			$this->setRedirect($redirect);

			return false;
		}

		// задает куку, означающую участие пользователя в опросе
		$this->input->cookie->set('multipoll'.$id, '1', time() + (3600 * 24 * 7), $app->get('cookie_path', '/'),
			$app->get('cookie_domain'));

		// очищает данные формы в сессии.
		$app->setUserState('com_multipolls.poll.data.'. $id, null);

		$this->setMessage(Text::_('COM_MULTIPOLLS_VOTES_SUCCESS'));
		$this->setRedirect($redirect);
	}
}
