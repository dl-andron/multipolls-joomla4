<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

?>

<?php if(isset($question->answers)) : ?>
	<div class='r-answers'>
		<?php foreach ($question->answers as $answer) :?>
			<label class='radio'>
				<input type='radio' name='poll[r][<?php echo $answer->id_question ?>]' value="<?php echo $answer->id ?>"
					<?php if($question->required) :?>
						required
					<?php endif; ?>

					<?php if($formData &&
						!empty($formData['r'][$answer->id_question]) &&
						$formData['r'][$answer->id_question] == $answer->id)
						:?>
						checked
					<?php endif; ?>
				>
				<?php echo $answer->name ?>

				<?php if($answer->img_url != '') :?>
					<img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
				<?php endif; ?>
			</label>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
