<?php

namespace DL\Component\Multipolls\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use DL\Component\Multipolls\Administrator\Helper\MultipollsHelper;

/**
 * Component Controller
 *
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 */
	protected $default_view = 'polls';

	public function display($cachable = false, $urlparams = array())
	{
		MultipollsHelper::installNewLanguages();
		return parent::display();
	}
}