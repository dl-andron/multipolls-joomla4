<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

$wa->useScript('com_multipolls.priority');

// после неудачной отправки ставим в том порядке, в каком отправлял пользователь
$orderingAnswers = [];
if(!empty($formData['priority']) && !empty($question->answers)){
	foreach($formData['priority'][$question->answers[0]->id_question] as $answer){
		$key = array_search($answer, array_column($question->answers, 'id'));
		$orderingAnswers[] = $question->answers[$key];
	}
}

if(!empty($orderingAnswers)){
	$question->answers = $orderingAnswers;
}

?>

<?php if(isset($question->answers)) : ?>
	<div class='pr-answers'>
		<ul class='priority-list'>
			<?php foreach ($question->answers as $answer) :?>
				<li>
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>

					<?php echo $answer->name ?>

					<?php if($answer->img_url != '') :?>
						<img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
					<?php endif; ?>

					<input type='hidden' name='poll[priority][<?php echo $answer->id_question ?>][]' value="<?php echo $answer->id ?>">
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
