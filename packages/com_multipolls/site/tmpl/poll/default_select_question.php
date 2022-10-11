<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

?>

<?php if(isset($this->answers)) : ?>
    <div class='s-answers'>
        <?php foreach ($this->answers as $answer) :?>
            <label for='select<?php echo $answer->id?>'><?php echo $answer->name ?></label>

            <select style='width:auto' name='poll[s][<?php echo $answer->id_question ?>-<?php echo $answer->id ?>]' id='select<?php echo $answer->id?>'>
                <?php // беру для данного вопроса максимальное значение ранжирования ?>
                <?php $range = $this->getModel()->getRangeValue($answer->id_question); ?>

	            <?php // беру для данного вопроса выбранный ответ ?>
	            <?php if(
		            $this->formData && $this->formData['s'][$answer->id_question.'-'.$answer->id]
	            ) :?>
		            <?php $selectedNum = $this->formData['s'][$answer->id_question.'-'.$answer->id] ?>
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

	        <?php if($answer->img_url != '') :?>
                <img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
	        <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
