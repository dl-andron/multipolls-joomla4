<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/** @var \DL\Component\Multipolls\Administrator\View\Poll\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_multipolls&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="poll-form"
      aria-label="<?php echo Text::_('COM_MULTIPOLLS_POLL_STAT', true); ?>"
      class="form-validate"
      >

    <h2><?php echo $this->escape($this->result->poll_name); ?></h2>

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
		endswitch;
		?>
	<?php endforeach; ?>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
