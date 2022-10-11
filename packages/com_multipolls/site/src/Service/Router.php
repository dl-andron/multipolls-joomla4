<?php

namespace DL\Component\Multipolls\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;

class Router extends RouterView
{
	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		$poll = new RouterViewConfiguration('poll');
		$poll->setKey('id');
		$this->registerView($poll);

		parent::__construct($app, $menu);

		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}
}
