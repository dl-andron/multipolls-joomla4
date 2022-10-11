<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var \DL\Component\Multipolls\Administrator\View\Question\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_multipolls&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="poll-form"
      aria-label="<?php echo Text::_('COM_MULTIPOLLS_QUESTION_FORM_TITLE_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
      class="form-validate">

    <div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab',
			['active' => 'details', 'recall' => true, 'breakpoint' => 768]
		); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_MULTIPOLLS_QUESTION_TAB')); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->form->renderFieldset('titles'); ?>
                    <?php echo $this->form->renderFieldset('details'); ?>
                    <?php echo $this->form->renderFieldset('own-names'); ?>
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
            <div class="row">
                <div class="col-md-6">
                    <fieldset id="fieldset-metadata" class="options-form">
                        <legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
                        <div>
                            <?php echo $this->form->renderFieldset('publish'); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
