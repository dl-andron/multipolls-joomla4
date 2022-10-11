<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

// добавляю языковую константу для вывода в js
Text::script('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS');

?>

<?php if(isset($this->answers)) : ?>
	<div class='cb-answers<?php if($this->required) :?> required<?php endif; ?>'>
        <?php foreach ($this->answers as $answer) :?>
            <label class='checkbox'>
                <input type='checkbox' name='poll[cb][<?php echo $answer->id_question ?>][]' value="<?php echo $answer->id?>"
	                <?php if(
                        $this->formData && !empty($this->formData['cb'][$answer->id_question]) &&
                        in_array($answer->id, $this->formData['cb'][$answer->id_question])
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
