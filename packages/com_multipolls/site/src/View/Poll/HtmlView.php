<?php

namespace DL\Component\Multipolls\Site\View\Poll;

use DL\Component\Multipolls\Site\Helper\MultipollsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class HtmlView extends BaseHtmlView
{
	/**
	 * Опрос
	 *
	 * @var  \stdClass
	 */
	protected $item;

	/**
	 * The page parameters
	 *
	 * @var    \Joomla\Registry\Registry|null
	 *
	 * @since  4.0.0
	 */
	protected $params = null;

	/**
	 * The model state
	 *
	 * @var   \Joomla\CMS\Object\CMSObject
	 */
	protected $state;

	/**
	 * Данные формы опроса
	 *
	 * @var  array
	 */
	protected $formData;

	/**
	 * Кука, означающая участие пользователя в опросе
	 *
	 * @var  integer
	 */
	protected $cookie;

	/**
	 * Проверочный код
	 *
	 * @var  string
	 */
	protected $captchaCode;

	/**
	 * Результаты опроса
	 *
	 * @var  array
	 */
	protected $result;

	/**
	 * The page class suffix
	 *
	 * @var    string
	 *
	 * @since  4.0.0
	 */
	protected $pageclass_sfx = '';

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  The template file to include
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function display($tpl = null): void
	{
		$this->cookie = $this->get('Cookie');
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// если уже проголосовал
		if($this->cookie){
			$this->result = $this->get('Stat');
			$this->setLayout('show');
		} else {
			$this->item = $this->get('Item');
			$this->captchaCode = MultipollsHelper::encryptCaptcha(MultipollsHelper::generateRandomString(6));
			$this->formData = $this->state->get('form.data');
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Получает html результатов
	 *
	 * @param $id integer Id опроса
	 *
	 * @return  string
	 *
	 */
	public function renderAjaxResults($id): string
	{
		$this->params = new Registry();
		$this->params->set('show_result_after_vote', 1);
		$this->params->set('is_ajax', 1);

		$statModel = $this->getModel('stat');
		$this->result = $statModel->getStat($id);

		$this->setLayout('show');

		return $this->loadTemplate();
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app = Factory::getApplication();

		/**
		 * Because the application sets a default page title,
		 * we need to get it from the menu item itself
		 */
		$menu = $app->getMenu()->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('COM_MULTIPOLLS'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $this->item->title;
		}

		$this->setDocumentTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}
	}
}
