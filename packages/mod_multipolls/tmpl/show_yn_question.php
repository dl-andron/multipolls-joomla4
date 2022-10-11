<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<?php if(!empty($question['votes'])) :?>
	<table class="table table-bordered">
		<tbody>
		<tr>
			<th><?php echo Text::_('MOD_MULTIPOLLS_ANSWER')?></th>
			<th><?php echo Text::_('MOD_MULTIPOLLS_YES')?></th>
			<th><?php echo Text::_('MOD_MULTIPOLLS_NO')?></th>
		</tr>
		<?php foreach ($question['votes'] as $answer) : ?>
			<tr>
				<td width="40%"><?php echo $answer->name; ?></td>
				<td><?php echo $answer->y; ?></td>
				<td><?php echo $answer->n; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>