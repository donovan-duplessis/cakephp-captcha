<!-- Inline CSS styling for reload anchor element -->
<?php
    echo $this->start('css');
    echo "<style>
            .reload {
              font-family: Verdana, Geneva, sans-serif;
              display: block;
            }
            fieldset {
              padding: 0;
            }
            a, img, input {
              padding: 0.3% 0.5%;
            }
          </style>";
    echo $this->end();
?>

<!-- Captcha form elements -->
<?php
    echo $this->Form->create('Contact');
    echo $this->Form->inputs(array('legend' => '* Now supports multiple captchas per form'));
    foreach($captcha_fields as $captcha) {
        echo $this->Html->image($captcha . '.jpg', array('id' => $captcha));
        echo $this->Html->link('reload image &#x21bb;', '#', array('class' => 'reload', 'escape' => false));
        echo $this->Form->input($captcha, array('label' => 'Captcha', 'value' => ''));

    }
    echo $this->Form->end('Submit');
?>

<!-- Javascript reload click event to reload captcha image -->
<?php
    $this->Js->get('.reload')->event('click',
        "$(this).prev().attr('src', $(this).prev().attr('src') + '?' + new Date().getTime())"
    );
?>
