<?php

namespace DL\Component\Multipolls\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 */
	protected $default_view = 'poll';

	public function display($cachable = false, $urlparams = false)
	{
		return parent::display();
	}
}