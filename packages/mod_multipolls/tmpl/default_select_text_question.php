<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use DL\Module\Multipolls\Site\Helper\MultipollsHelper;

?>

<?php if(isset($question->answers)) : ?>
	<div class='sta-answers'>
		<?php foreach ($question->answers as $answer) :?>
			<label for='select<?php echo $answer->id ?>'><?php echo $answer->name ?></label>

			<select style='width:auto' name='poll[sta][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]' id='select<?php echo $answer->id ?>'>
				<?php // беру для данного вопроса максимальное значение ранжирования ?>
				<?php $range = MultipollsHelper::getRangeValue($answer->id_question); ?>

				<?php // беру для данного вопроса выбранный ответ ?>
				<?php if(
					$formData && $formData['sta'][$answer->id_question.'-'.$answer->id]
				) :?>
					<?php $selectedNum = $formData['sta'][$answer->id_question.'-'.$answer->id] ?>
				<?php endif; ?>

				<?php for ($i = 1; $i <= $range; $i++) :?>
					<option
						<?php if(!empty($selectedNum) && $i == $selectedNum): ?>
							selected
						<?php endif; ?>
					>
						<?php echo $i ?>
					</option>
				<?php endfor; ?>
			</select>

			<?php if($formData && !empty($formData['sta-text'][$answer->id_question.'-'.$answer->id])) :?>
				<?php $textVal = trim($formData['sta-text'][$answer->id_question.'-'.$answer->id]); ?>
			<?php else :?>
				<?php $textVal = ''; ?>
			<?php endif ?>

			<textarea name='poll[sta-text][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]'
                <?php if($question->required) :?>
	                required
                <?php endif; ?>
            ><?php echo $textVal ?></textarea>
			<?php if($answer->img_url != '') :?>
				<img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>