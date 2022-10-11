<?php

namespace DL\Component\Multipolls\Administrator\Extension;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use DL\Component\Multipolls\Administrator\Service\Html\Question;
use DL\Component\Multipolls\Administrator\Service\Html\Answer;
use Joomla\Database\DatabaseInterface;

/**
 * Component class for com_multipolls
 *
 * @since  4.0.0
 */
class MultipollsComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface
{
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function boot(ContainerInterface $container)
	{
		$question = new Question;
		$question->setDatabase($container->get(DatabaseInterface::class));

		$answer = new Answer;
		$answer->setDatabase($container->get(DatabaseInterface::class));

		$registry = $this->getRegistry();

		$registry->register('question', $question);
		$registry->register('answer', $answer);
	}
}
