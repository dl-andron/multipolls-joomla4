<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;

$wa->useStyle('com_multipolls.styles')
    ->useScript('com_multipolls.form-events');
?>

<div class="mod-multipolls-poll results">
	<?php if ($params->get('show_poll_name', 0)) :?>
        <h2 itemprop="headline"<?php if ($params->get('slider-title', 0)): ?>class="slider-title"<?php endif;?>>
	        <?php echo $result->poll_name; ?>
        </h2>
	<?php endif; ?>
    <div class="poll-body">
	    <?php if($params->get('show_result_after_vote')) :?>
		    <?php foreach ($result->questions as $question) : ?>
                <h3><?php echo $question['name'] ?></h3>

			    <?php switch ($question['type']) :
				    case '1':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_radio_question');
					    break;
				    case '2':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_cb_question');
					    break;
				    case '3':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_select_question');
					    break;
				    case '4':
					    break;
				    case '5':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_select_text_question');
					    break;
				    case '6':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_radio_own_question');
					    break;
				    case '7':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_yn_question');
					    break;
				    case '8':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_cb_own_question');
					    break;
				    case '9':
					    require ModuleHelper::getLayoutPath('mod_multipolls', 'show_priority_question');
					    break;
				    default:
					    break;
			    endswitch;
			    ?>
		    <?php endforeach; ?>
	    <?php else :?>
		    <?php
                $system_message = $app->getMessageQueue();
		    ?>
		    <?php $message = !empty($system_message[0]["message"]) ? Text::_('MOD_MULTIPOLLS_THANKS') :
			    Text::_('MOD_MULTIPOLLS_ALREADY_VOTED'); ?>
		    <?php echo $message; ?>
	    <?php endif; ?>
    </div>
</div>
