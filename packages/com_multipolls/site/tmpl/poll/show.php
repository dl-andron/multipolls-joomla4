<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_multipolls.styles');

?>

<div class="com-multipolls-poll results<?php echo $this->pageclass_sfx; ?>">
	<?php if($this->params->get('show_poll_name')) :?>
        <h2 itemprop="headline">
            <?php echo $this->escape($this->result->poll_name); ?>
        </h2>
	<?php endif; ?>

	<?php if($this->params->get('show_result_after_vote')) :?>
		<?php foreach ($this->result->questions as $question) : ?>
            <h3><?php echo $question['name'] ?></h3>

			<?php $this->question = $question['votes']; ?>

			<?php switch ($question['type']) :
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
			endswitch;
			?>
		<?php endforeach; ?>

	    <?php if($this->params->get('is_ajax')) :?>
            <button type="button" class="btn btn-primary back-to-poll">
                <?php echo Text::_('COM_MULTIPOLLS_BACK_TO_POLL') ?>
            </button>
        <?php endif; ?>

	<?php else :?>
        <?php
		    $app = Factory::getApplication();
		    $system_message = $app->getMessageQueue();
        ?>
		<?php $message = !empty($system_message[0]["message"]) ? Text::_('COM_MULTIPOLLS_THANKS') :
            Text::_('COM_MULTIPOLLS_ALREADY_VOTED'); ?>
		<?php echo $message; ?>
	<?php endif; ?>
</div>
