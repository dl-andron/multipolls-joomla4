<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

?>

<?php if(isset($question->answers)) : ?>
	<div class='ta-answers'>
		<?php foreach ($question->answers as $answer) :?>
			<p><?php echo $answer->name ?></p>

			<?php if($formData && !empty($formData['ta'][$answer->id_question.'-'.$answer->id])) :?>
				<?php $textVal = trim($formData['ta'][$answer->id_question.'-'.$answer->id]); ?>
			<?php else :?>
				<?php $textVal = ''; ?>
			<?php endif ?>

			<textarea name='poll[ta][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]'
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