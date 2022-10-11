<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;

HTMLHelper::_('behavior.keepalive');

// возможность показа вопросов по одному
if(count(array_diff(array_column($poll->questions, 'id_type'), [1, 6, 7])) == 0
	&& $params->get('show_one_by_one')) {
	$wa->useScript('com_multipolls.one-by-one');

	$app->getDocument()->addScriptOptions('mod-multipolls-options',
		[
			'is_module' => '1',
		]
	);
}

if($params->get('show_result_button')){
	$wa->useScript('com_multipolls.results');
}

$wa->useScript('com_multipolls.captcha')
	->useStyle('com_multipolls.captcha')
	->useStyle('com_multipolls.styles')
	->useScript('com_multipolls.form-events')
	->useScript('com_multipolls.validation');
?>

<div class="mod-multipolls-poll">
    <?php if ($params->get('show_poll_name', 0)) :?>
        <h2 itemprop="headline"<?php if ($params->get('slider-title', 0)): ?>class="slider-title"<?php endif;?>>
            <?php echo $poll->name; ?>
        </h2>
    <?php endif; ?>

    <div class="poll-body">
        <?php if ($params->get('show_poll_text', 0)) :?>
            <div class="poll-text">
                <?php echo $poll->text ?>
            </div>
        <?php endif; ?>

        <form id="poll-form-<?php echo $module->id; ?>" class="mod-multipolls poll" action="<?php echo Route::_('index.php', true); ?>" method="post">
            <?php foreach ($poll->questions as $question) :?>
                <div class='answers'>
                    <h4>
                        <?php echo $question->name ?>
                        <?php if($question->required) :?>
                            <span style='color:#ff0000; font-size:9px; vertical-align:top;'> *</span>
                        <?php endif; ?>
                    </h4>

                    <?php if($question->image) :?>
                        <div class='img-question'>
                            <img src="<?php echo $question->image; ?>">
                        </div>
                    <?php endif; ?>

                    <?php switch ($question->id_type) :
                        case '1':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_radio_question');
                            break;
                        case '2':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_cb_question');
                            break;
                        case '3':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_select_question');
                            break;
                        case '4':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_text_question');
                            break;
                        case '5':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_select_text_question');
                            break;
                        case '6':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_radio_own_question');
                            break;
                        case '7':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_yn_question');
                            break;
                        case '8':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_cb_own_question');
                            break;
                        case '9':
                            require ModuleHelper::getLayoutPath('mod_multipolls', 'default_priority_question');
                            break;
                        default:
                            break;
                    endswitch; ?>
                </div>
            <?php endforeach; ?>

            <div class="control-group captcha">
                <div class="control-label">
                    <label for="captcha"><?php echo Text::_('MOD_MULTIPOLLS_CAPTCHA') ?><span> *</span></label>
                </div>
                <div class="controls">
                    <input type="hidden" class="real-captcha" name="real-captcha" value="<?php echo $captchaCode ?>" >
                    <img src="<?php echo URI::base()?>?option=com_multipolls&task=captcha.render&code=<?php echo $captchaCode ?>"
                         class="captcha-pic" alt="captcha"/>
                    <span class="icon-refresh refresh-captcha"></span>
                    <input class="form-control required captcha-input" type="text" name="captcha" maxlength="6"
                           required autocomplete="off">
                </div>
            </div>

            <div class="mod-mulipolls__submit control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary submit-button">
                        <?php echo Text::_('MOD_MULTIPOLLS_SEND_VOTE') ?>
                    </button>
                    <?php if($params->get('show_result_button')) :?>
                        <button type="button" class="btn btn-primary result-button">
                            <?php echo Text::_('MOD_MULTIPOLLS_RESULTS') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo $poll->id ?>">
            <input type="hidden" name="option" value="com_multipolls">
            <input type="hidden" name="task" value="poll.savevote">
            <input type="hidden" name="return" value="<?php echo base64_encode(Uri::getInstance()); ?>">
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>

        <?php if($params->get('show_result_button')) :?>
            <div class="results"></div>
        <?php endif; ?>
    </div>
</div>
