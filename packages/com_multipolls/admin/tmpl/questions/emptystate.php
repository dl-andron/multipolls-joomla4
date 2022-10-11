<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_MULTIPOLLS_QUESTIONS',
	'formURL'    => 'index.php?option=com_multipolls&view=questions',
	'icon'       => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_multipolls') || count($user->getAuthorisedCategories('com_multipolls', 'core.create')) > 0) {
	$displayData['createURL'] = 'index.php?option=com_multipolls&task=question.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
