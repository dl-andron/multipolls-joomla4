<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_multipolls.priority');

// после неудачной отправки ставим в том порядке, в каком отправлял пользователь
$orderingAnswers = [];
if(!empty($this->formData['priority']) && !empty($this->answers)){
    foreach($this->formData['priority'][$this->answers[0]->id_question] as $answer){
	    $key = array_search($answer, array_column($this->answers, 'id'));
	    $orderingAnswers[] = $this->answers[$key];
    }
}

if(!empty($orderingAnswers)){
	$this->answers = $orderingAnswers;
}

?>

<?php if(isset($this->answers)) : ?>
    <div class='pr-answers'>
        <ul class='priority-list'>
            <?php foreach ($this->answers as $answer) :?>
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
