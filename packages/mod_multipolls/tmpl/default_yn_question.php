<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

?>

<?php if(isset($question->answers)) : ?>
	<div class='yn-answers'>
		<?php foreach ($question->answers as $answer) :?>
			<label for='select<?php echo $answer->id?>'><?php echo $answer->name ?></label>

			<label style='width: 50px;display: inline-block;'>
				<input type='radio' name='poll[yn][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]' value='y'
					<?php if($question->required) :?>
						required
					<?php endif; ?>

					<?php if(
						$formData &&
						!empty($formData['yn'][$answer->id_question.'-'.$answer->id]) &&
						$formData['yn'][$answer->id_question.'-'.$answer->id] == 'y'
					) :?>
						checked
					<?php endif ?>
				>
				<?php echo Text::_('MOD_MULTIPOLLS_YES') ?>
			</label>

			<label style='width: 50px;display: inline-block;'>
				<input type='radio' name='poll[yn][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]' value='n'
					<?php if($question->required) :?>
						required
					<?php endif; ?>

					<?php if(
						$formData &&
						!empty($formData['yn'][$answer->id_question.'-'.$answer->id]) &&
						$formData['yn'][$answer->id_question.'-'.$answer->id] == 'n'
					) :?>
						checked
					<?php endif ?>
				>
				<?php echo Text::_('MOD_MULTIPOLLS_NO') ?>
			</label>

			<?php if($answer->img_url != '') :?>
				<img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
