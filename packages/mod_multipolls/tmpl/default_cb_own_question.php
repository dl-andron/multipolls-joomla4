<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

// добавляю языковую константу для вывода в js
Text::script('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS');

?>

<?php if(isset($question->answers)) : ?>
	<div class='cbo-answers<?php if($question->required) :?> required<?php endif; ?>'>
		<?php foreach ($question->answers as $answer) :?>
			<label class='checkbox'>
				<input type='checkbox' name='poll[cbo][<?php echo $answer->id_question ?>][]' value="<?php echo $answer->id ?>"
					<?php if(
						$formData && !empty($formData['cbo'][$answer->id_question]) &&
						in_array($answer->id, $formData['cbo'][$answer->id_question])
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

		<label class='checkbox'>
			<input type='checkbox' class='own-checkbox' name='poll[cbo][<?php echo $answer->id_question ?>][]' value='custom'
				<?php if(
					$formData && !empty($formData['cbo'][$answer->id_question]) &&
					in_array('custom', $formData['cbo'][$answer->id_question])
				) :?>
					checked
				<?php endif; ?>
			>

			<?php if (trim($question->nameOwn) != '') :?>
				<?php echo $question->nameOwn ?>
			<?php endif; ?>

			<input class='own-input' name='poll[cbo][custom-<?php echo $answer->id_question ?>]'

				<?php if (trim($question->nameOwn) != '') :?>
					style='margin-left: 10px;'
				<?php endif; ?>

				   type='text' placeholder='<?php echo Text::_("COM_MULTIPOLLS_OWN_ANSWER")?>'

				<?php if(
					$formData && !empty($formData['cbo'][$answer->id_question]) &&
					array_key_exists('custom-'.$answer->id_question, $formData['cbo'])
				) :?>
					value="<?php echo $formData['cbo']['custom-'.$answer->id_question] ?>"
				<?php endif; ?>
			>
		</label>
	</div>
<?php endif; ?>
