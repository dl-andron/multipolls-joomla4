<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();

// возможность показа вопросов по одному
if(count(array_diff(array_column($this->item->questions, 'id_type'), [1, 6, 7])) == 0
    && $this->item->params->get('show_one_by_one')) {
	$wa->useScript('com_multipolls.one-by-one');
}

if($this->item->params->get('show_result_button')){
	$wa->useScript('com_multipolls.results');
}

$wa->useScript('com_multipolls.captcha')
	->useStyle('com_multipolls.captcha')
	->useStyle('com_multipolls.styles')
	->useScript('com_multipolls.form-events')
	->useScript('com_multipolls.validation');
?>

<div class="com-multipolls-poll item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading') != 0) : ?>
        <h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
	<?php endif; ?>

	<?php if($this->item->params->get('show_poll_name')): ?>
        <h2 itemprop="headline">
	        <?php echo $this->escape($this->item->name); ?>
        </h2>
	<?php endif; ?>

    <div class="text">
		<?php echo $this->item->text; ?>
    </div>

    <form method="post" class="poll" action="<?php echo Route::_('index.php?option=com_multipolls&task=poll.savevote'); ?>">
	    <?php foreach ($this->item->questions as $question) :?>
            <div class='answers'>
                <h4>
                    <?php echo $this->escape($question->name) ?>
                    <?php if($question->required) :?>
                        <span style='color:#ff0000; font-size:9px; vertical-align:top;'> *</span>
                    <?php endif; ?>
                </h4>

                <?php if($question->image) :?>
                    <div class='img-question'>
                        <img src="<?php echo $question->image; ?>">
                    </div>
                <?php endif; ?>

                <?php $this->answers = $question->answers ?>
                <?php $this->required = $question->required ?>
                <?php $this->nameOwn = $question->name_own ?>

                <?php switch ($question->id_type) :
                    case '1':
                        echo $this->loadTemplate('radio_question');
                        break;
                    case '2':
                        echo $this->loadTemplate('cb_question');
                        break;
                    case '3':
                        echo $this->loadTemplate('select_question');
                        break;
                    case '4':
                        echo $this->loadTemplate('text_question');
                        break;
                    case '5':
                        echo $this->loadTemplate('select_text_question');
                        break;
                    case '6':
                        echo $this->loadTemplate('radio_own_question');
                        break;
                    case '7':
                        echo $this->loadTemplate('yn_question');
                        break;
                    case '8':
                        echo $this->loadTemplate('cb_own_question');
                        break;
                    case '9':
                        echo $this->loadTemplate('priority_question');
                        break;
                    default:
                        break;
                endswitch; ?>
            </div>
	    <?php endforeach; ?>

        <div class="control-group captcha">
            <div class="control-label">
                <label for="captcha"><?php echo Text::_('COM_MULTIPOLLS_CAPTCHA') ?><span> *</span></label>
            </div>
            <div class="controls">
                <input type="hidden" class="real-captcha" name="real-captcha" value="<?php echo $this->captchaCode ?>" >
                <img src="<?php echo URI::base()?>?option=com_multipolls&task=captcha.render&code=<?php echo $this->captchaCode ?>"
                    class="captcha-pic" alt="captcha"/>
                <span class="icon-refresh refresh-captcha"></span>
                <input class="form-control required captcha-input" type="text" name="captcha" maxlength="6"
                    required autocomplete="off">
            </div>
        </div>

        <div class="com-mulipolls__submit control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary submit-button">
					<?php echo Text::_('COM_MULTIPOLLS_SEND_VOTE') ?>
                </button>
	            <?php if($this->item->params->get('show_result_button')) :?>
                    <button type="button" class="btn btn-primary result-button">
	                    <?php echo Text::_('COM_MULTIPOLLS_RESULTS') ?>
                    </button>
	            <?php endif; ?>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->item->id ?>">
        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::getInstance()); ?>">

		<?php echo HTMLHelper::_('form.token'); ?>
    </form>

	<?php if($this->item->params->get('show_result_button')) :?>
        <div class="results"></div>
	<?php endif; ?>
</div>
