<?php

defined('_JEXEC') or die;

$sum = 0;

foreach ($question['votes']->votes as $key => $answer){
	$sum += $answer->count;
}

?>

<?php if(!empty($question['votes'])): ?>
	<table class="table table-bordered">
		<tbody>
		<?php foreach ($question['votes']->votes as $key => $answer) : ?>
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
<?php endif; ?>
