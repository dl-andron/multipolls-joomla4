<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \DL\Component\Multipolls\Administrator\View\Questions\HtmlView $this */

?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<button type="submit" class="btn btn-success" onclick="Joomla.submitbutton('question.batch');return false;">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
