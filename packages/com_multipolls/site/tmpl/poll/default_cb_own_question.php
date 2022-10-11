<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

// добавляю языковую константу для вывода в js
Text::script('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS');

?>

<?php if(isset($this->answers)) : ?>
	<div class='cbo-answers<?php if($this->required) :?> required<?php endif; ?>'>
        <?php foreach ($this->answers as $answer) :?>
            <label class='checkbox'>
                <input type='checkbox' name='poll[cbo][<?php echo $answer->id_question ?>][]' value="<?php echo $answer->id ?>"
	                <?php if(
		                $this->formData && !empty($this->formData['cbo'][$answer->id_question]) &&
		                in_array($answer->id, $this->formData['cbo'][$answer->id_question])
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
		            $this->formData && !empty($this->formData['cbo'][$answer->id_question]) &&
		            in_array('custom', $this->formData['cbo'][$answer->id_question])
	            ) :?>
                    checked
	            <?php endif; ?>
            >

		    <?php if (trim($this->nameOwn) != '') :?>
			    <?php echo $this->nameOwn ?>
		    <?php endif; ?>

            <input class='own-input' name='poll[cbo][custom-<?php echo $answer->id_question ?>]'

                <?php if (trim($this->nameOwn) != '') :?>
                    style='margin-left: 10px;'
                <?php endif; ?>

                type='text' placeholder='<?php echo Text::_("COM_MULTIPOLLS_OWN_ANSWER")?>'

	            <?php if(
		            $this->formData && !empty($this->formData['cbo'][$answer->id_question]) &&
		            array_key_exists('custom-'.$answer->id_question, $this->formData['cbo'])
	            ) :?>
                    value="<?php echo $this->formData['cbo']['custom-'.$answer->id_question] ?>"
	            <?php endif; ?>
            >
        </label>
    </div>
<?php endif; ?>