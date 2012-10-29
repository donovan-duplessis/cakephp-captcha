# Captcha implementation for CakePHP 2.2

Generates an image with random alphanumeric characters which requires a human to validate.  This is to prevent automated spam and spam bots.

Features:

+ Random alphanumeric text
+ Supports rotation of text
+ The image width and height dimensions can be set
+ The font size can be adjusted
+ Random monospace fonts are used during generation (anonymous, droidsans, ubuntu)

## Website

http://donovan-duplessis.github.com/cakephp-captcha

## Requirements

+ PHP version: 5.2+
+ CakePHP version: 2.0+

## Installation

Clone or download the component:

    git clone git://github.com/donovan-duplessis/cakephp-captcha.git

Copy the component and behavior into your framework at:

    cd cakephp-captcha
    cp Controllers/Components/CaptchaComponent.php <your-app>/Controllers/Components/
    cp Models/Behaviors/CaptchaBehavior.php <your-app>/Models/Behaviors/CaptchaBehavior.php

Copy the fonts into your framework at:

    cp -R Lib/Fonts <your-app>/Lib/
    chmod 755 <your-app>/Lib/Fonts

## Usage

Include Captcha behavior in model:<br/>

    public $actsAs = array('Captcha');

Include Captcha component in controller:

    public $components = array('Captcha');

To output the captcha image from controller:

    $this->Captcha->generate();

## Sample Code

Model Contact.php

    <?php
    App::uses('AppModel', 'Model');
    class Contact extends AppModel {
        public $actsAs = array(
            'Captcha' => array(
                'field' => 'captcha',
                'error' => 'Captcha code entered invalid'
            )
        );
    }
    ?>

Controller ContactsController.php

    <?php
    App::uses('AppController', 'Controller');
    class ContactsController extends AppController {
        public $components = array(
            'Captcha' => array(
                'rotate' => true
            )
        );

        public function captcha()  {
            $this->autoRender = false;
            $this->Captcha->generate();
        }

        public function index() {
            if ($this->RequestHandler->isPost()) {
                $this->Contact->setCaptcha($this->Captcha->getCode());
                $this->Contact->set($this->data);
                if ($this->Contact->validates()) {
                    $this->Session->setFlash('Captcha code validated successfully',
                        'flash_good');
                }
            }
        }
    }
    ?>

Route Config/routes.php

    Router::connect('/img/captcha.jpg', array('controller' => 'contacts', 'action' => 'captcha'));

View Contacts/index.ctp

    <?php
        echo $this->Form->create('Contact');
        echo $this->Html->image('captcha.jpg', array('style' => 'padding: 0.5%;'));
        echo $this->Form->input('captcha');
        echo $this->Form->end('Send');
    ?>

## License

Licensed under The MIT License<br/>
Redistributions of files must retain the above copyright notice.

## Copyright

Copyright (C) Donovan du Plessis, donodp@gmail.com

## Contributors

Adriano Luís Rocha, <driflash@gmail.com>

## Changelog

### Component

### 1.3

* Modify font directory path to <app>/Lib/Fonts (Adriano Luís Rocha)

### 1.2

* Convert plugin to version 2.x of CakePHP Framework (Adriano Luís Rocha)

### 1.1

* Rename __uniqueCode method to __randomCode
* Use characters configuration parameter for length in __randomCode method
* Add characters parameter to default configuration to specify number of characters to display in image

### 1.0

* Initial version

### Behavior

### 1.3

* Access Model reference correctly (Adriano Luís Rocha)

### 1.2

* Change class to extend ModelBehavior - 2.0 compliant (Adriano Luís Rocha)

### 1.1

* Extract default configuration settings into class variable

### 1.0

* Initial version
