<?php

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use DL\Module\Multipolls\Site\Helper\MultipollsHelper;
use DL\Component\Multipolls\Site\Helper\MultipollsHelper as MultipollsComponentHelper;

$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_multipolls');

$id = $params->get('id_poll', '');

if ($app->input->get->cookie->get('multipoll'. $id)){
	$result = MultipollsHelper::getResult($id);
	require ModuleHelper::getLayoutPath('mod_multipolls', 'show');
	return;
}

$poll = MultipollsHelper::getPoll($id);
$captchaCode = MultipollsComponentHelper::encryptCaptcha(MultipollsComponentHelper::generateRandomString(6));
$formData = $app->getUserState('com_multipolls.poll.data.'. $id);

$layout = $params->get('layout', 'default');
require ModuleHelper::getLayoutPath('mod_multipolls', $layout);
