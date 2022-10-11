<?php

defined('_JEXEC') or die;

?>

<?php if(!empty($this->question)) :?>
	<?php foreach ($this->question as $key => $answer) :?>
		<h5><?php echo $answer->name ?></h5>
		<?php if(isset($answer->answers)) :?>
			<?php foreach ($answer->answers as $k => $value) :?>
				<div>
					<?php echo $k; ?> (<?php echo $value; ?>)<br>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
