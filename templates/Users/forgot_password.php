<?php
echo $this->Form->create();
echo $this->Form->input('email');
echo $this->Form->submit('Reset Password', array('class' => 'button'));
echo $this->Form->end();
?>