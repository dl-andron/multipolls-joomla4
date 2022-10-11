<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

// добавляю языковую константу для вывода в js
Text::script('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS');

?>

<?php if(isset($question->answers)) : ?>
	<div class='cb-answers<?php if($question->required) :?> required<?php endif; ?>'>
		<?php foreach ($question->answers as $answer) :?>
			<label class='checkbox'>
				<input type='checkbox' name='poll[cb][<?php echo $answer->id_question ?>][]' value="<?php echo $answer->id?>"
					<?php if(
						$formData && !empty($formData['cb'][$answer->id_question]) &&
						in_array($answer->id, $formData['cb'][$answer->id_question])
					) :?>
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
