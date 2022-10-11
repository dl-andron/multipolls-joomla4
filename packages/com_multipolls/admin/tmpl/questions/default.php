<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/** @var \DL\Component\Multipolls\Administrator\View\Questions\HtmlView $this */

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
	->useScript('multiselect');

$user = Factory::getUser();

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_multipolls&view=questions'); ?>" method="post" name="adminForm" id="adminForm">
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
					<table class="table itemList" id="questionList">
						<caption class="visually-hidden">
							<?php echo Text::_('COM_MULTIPOLLS_QUESTIONS_TABLE_CAPTION'); ?>,
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
                                <th scope="col" class="w-10 d-none d-md-table-cell text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_CREATED', 'created', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                                </th>
							</tr>
						</thead>
						<tbody>
                            <?php foreach ($this->items as $i => $item) :
	                            $canChange  = $user->authorise('core.edit.state', 'com_multipolls');
	                            $canEdit    = $user->authorise('core.edit', 'com_multipolls');
                            ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
		                            <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="text-center">
		                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'questions.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                                </td>
                                <th scope="row">
                                    <div class="break-word">
			                            <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_multipolls&task=question.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->name); ?>">
					                            <?php echo $this->escape($item->name); ?></a>
			                            <?php else : ?>
				                            <?php echo $this->escape($item->name); ?>
			                            <?php endif; ?>
                                    </div>
                                    <div class="small">
                                        <?php echo Text::_('COM_MULTIPOLLS_POLL') . ': '; ?>
	                                    <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_multipolls&task=poll.edit&id=' . (int) $item->id_poll); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->poll_name); ?>">
			                                    <?php echo $this->escape($item->poll_name); ?></a>
	                                    <?php else : ?>
		                                    <?php echo $this->escape($item->poll_name); ?>
	                                    <?php endif; ?>
                                    </div>
                                </th>
                                <td class="small d-none d-md-table-cell text-center">
		                            <?php
		                            echo $item->created > 0 ? HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) : '-';
		                            ?>
                                </td>
                                <td class="d-none d-md-table-cell">
		                            <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
						</tbody>
					</table>

					<?php // load the pagination. ?>
					<?php echo $this->pagination->getListFooter(); ?>

					<?php // Load the batch processing form. ?>
					<?php if (
						$user->authorise('core.create', 'com_multipolls')
						&& $user->authorise('core.edit', 'com_multipolls')
						&& $user->authorise('core.edit.state', 'com_multipolls')
					) : ?>
						<?php echo HTMLHelper::_(
							'bootstrap.renderModal',
							'collapseModal',
							[
								'title' => Text::_('COM_MULTIPOLLS_QUESTIONS_BATCH_OPTIONS'),
								'footer' => $this->loadTemplate('batch_footer')
							],
							$this->loadTemplate('batch_body')
						); ?>
					<?php endif; ?>
				<?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
