<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<?php if(!empty($this->question)): ?>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<th><?php echo Text::_('COM_MULTIPOLLS_ANSWER')?></th>
				<th><?php echo Text::_('COM_MULTIPOLLS_COUNT_VOTES_BY_PRIORITY')?></th>
			</tr>
			<?php foreach ($this->question as $answer) : ?>
				<?php $answerSum = array_sum($answer->counts); ?>
				<tr>
					<td width="40%"><?php echo $answer->name; ?></td>
					<td>
						<?php foreach ($answer->counts as $selectedPriority => $sumVotes) : ?>
							<?php $percent = $sumVotes != 0 ? round($sumVotes / $answerSum * 100,2) : 0; ?>
							<div>
								<?php echo $selectedPriority.' - '.$sumVotes." (".$percent."%)"; ?>
							</div>
						<?php endforeach; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>	