<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

?>

<?php if(isset($this->answers)) : ?>
    <div class='ro-answers'>
	    <?php foreach ($this->answers as $answer) :?>
            <label class='radio'>
                <input type='radio' name='poll[ro][<?php echo $answer->id_question ?>]' value="<?php echo $answer->id?>"
		            <?php if($this->required) :?>
                        required
		            <?php endif; ?>

	                <?php if($this->formData &&
                        !empty($this->formData['ro'][$answer->id_question]) &&
                        $this->formData['ro'][$answer->id_question] == $answer->id) :?>
                        checked
	                <?php endif; ?>
                >
	            <?php echo $answer->name ?>
	            <?php if($answer->img_url != '') :?>
                    <img src="<?php echo Uri::base(true)?>/<?php echo $answer->img_url ?>">
	            <?php endif; ?>
            </label>
	    <?php endforeach; ?>

        <label class='radio'>
            <input type='radio' class='own-radio' name='poll[ro][<?php echo $answer->id_question ?>]' value='custom'
                <?php if($this->required) :?>
                    required
                <?php endif; ?>

	            <?php if($this->formData &&
                    !empty($this->formData['ro'][$answer->id_question]) &&
		            $this->formData['ro'][$answer->id_question] == 'custom'
                ) :?>
                    checked
	            <?php endif; ?>
            >

            <?php if (trim($this->nameOwn) != '') :?>
                <?php echo $this->nameOwn ?>
            <?php endif; ?>

            <input class='own-input' name='poll[ro][custom-<?php echo $answer->id_question ?>]'

                <?php if (trim($this->nameOwn) != '') :?>
                    style='margin-left: 10px;'
                <?php endif; ?>

                type='text' placeholder='<?php echo Text::_("COM_MULTIPOLLS_OWN_ANSWER")?>'

	            <?php if($this->formData && !empty($this->formData['ro']['custom-'.$answer->id_question])) :?>
                    value="<?php echo $this->formData['ro']['custom-'.$answer->id_question] ?>"
	            <?php endif; ?>
            >
        </label>
    </div>
<?php endif; ?>