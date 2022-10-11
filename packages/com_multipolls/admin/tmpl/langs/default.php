<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/** @var \DL\Component\Multipolls\Administrator\View\Langs\HtmlView $this */

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
	->useScript('multiselect');

$user = Factory::getUser();

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_multipolls&view=langs'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
				<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
				?>
	            <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
			            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
	            <?php else : ?>
                    <table class="table itemList" id="langList">
                        <caption class="visually-hidden">
		                    <?php echo Text::_('COM_MULTIPOLLS_LANGS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                        <tr>
                            <td class="w-1 text-center">
			                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-1 text-center">
			                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width:100px">
			                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
			                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
	                    <?php foreach ($this->items as $i => $item) :
		                    $canChange  = $user->authorise('core.edit.state', 'com_multipolls');
		                    ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
				                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="text-center">
				                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'langs.', $canChange, 'cb'); ?>
                                </td>
                                <th scope="row">
                                    <div class="break-word">
	                                    <?php echo $this->escape($item->name); ?>
                                    </div>
                                </th>
                                <td class="d-none d-md-table-cell">
				                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
	                    <?php endforeach ?>
                        </tbody>
                    </table>

                    <?php // load the pagination. ?>
                    <?php echo $this->pagination->getListFooter(); ?>
	            <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
	            <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
