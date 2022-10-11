<?php

defined('_JEXEC') or die;

?>

<?php if(!empty($question['votes'])) : ?>
	<?php $count_votes = count($question['votes']->votes) ?>
	<?php foreach ($question['votes']->names as $key => $name) : ?>
		<table class="table table-bordered">
			<tbody>
			<?php $percents = $name->count != 0 ? round($name->count / $count_votes * 100,2) : 0; ?>
			<tr>
				<td width="40%"><?php echo $name->name; ?></td>
				<td>
					<div class="progress">
						<div class="progress-bar" role="progressbar" style="width:<?php echo $percents ?>%"
						     aria-valuenow="<?php echo $percents ?>" aria-valuemin="0" aria-valuemax="100">
							<div><?php echo $name->count ?> - <?php echo $percents ?> %</div>
						</div>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	<?php endforeach; ?>
<?php endif; ?>
