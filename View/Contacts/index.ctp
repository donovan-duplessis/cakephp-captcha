<!-- Inline CSS styling for reload anchor element -->
<?php
    echo $this->start('css');
    echo "<style>
            .pad {
              padding: 0.5%;
            }
            #reload {
              font-family: Verdana, Geneva, sans-serif;
              display: block;
            }
          </style>";
    echo $this->end();
?>

<!-- Captcha form elements -->
<?php
    echo $this->Form->create('Contact');
    echo $this->Html->image('captcha.jpg', array('id' => 'captcha', 'class' => 'pad'));
    echo $this->Html->link('reload image &#x21bb;', '#', array('id' => 'reload', 'class' => 'pad', 'escape' => false));
    echo $this->Form->input('captcha', array('value' => ''));
    echo $this->Form->end('Submit');
?>

<!-- Javascript reload click event to reload captcha image -->
<?php
    $this->Js->get('#reload')->event('click',
        "$('#captcha').attr('src', $('#captcha').attr('src') + '?' + new Date().getTime())"
    );
?>
