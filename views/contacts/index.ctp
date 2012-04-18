<?php
    echo $this->Form->create('Contact');
    echo $this->Html->image('captcha.jpg', array('style' => 'padding: 0.5%;'));
    echo $this->Form->input('captcha');
    echo $this->Form->end('Send');
?>
