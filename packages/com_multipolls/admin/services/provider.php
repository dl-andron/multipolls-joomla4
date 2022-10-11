<?php

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use DL\Component\Multipolls\Administrator\Extension\MultipollsComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {

	public function register(Container $container): void {
		$container->registerServiceProvider(new MVCFactory('\\DL\\Component\\Multipolls'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\DL\\Component\\Multipolls'));
		$container->registerServiceProvider(new RouterFactory('\\DL\\Component\\Multipolls'));
		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new MultipollsComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));
				$component->setRegistry($container->get(Registry::class));

				return $component;
			}
		);
	}
};