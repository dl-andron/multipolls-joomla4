<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$sum = 0;

foreach ($this->question->votes as $key => $answer){
	$sum += $answer->count;
}

?>

<?php if(!empty($this->question)): ?>
	<table class="table table-bordered">
		<tbody>
			<?php foreach ($this->question->votes as $key => $answer) : ?>
				<?php $percents = $answer->count != 0 ? round($answer->count / $sum * 100,2) : 0; ?>
				<tr>
					<td width="40%"><?php echo $answer->name; ?></td>
					<td>
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width:<?php echo $percents ?>%"
							     aria-valuenow="<?php echo $percents ?>" aria-valuemin="0" aria-valuemax="100">
								<div><?php echo $answer->count ?> - <?php echo $percents ?> %</div>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

    <h4><?php echo Text::_('COM_MULTIPOLLS_OWN_ANSWERS') ?></h4>

	<?php foreach ($this->question->textVotes as $textVote) : ?>
        <p><?php echo $textVote; ?></p>
	<?php endforeach; ?>
<?php endif; ?>
