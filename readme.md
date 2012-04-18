# Captcha implementation for CakePHP 1.3

Generates an image with random alphanumeric characters which requires a human to validate.  This is to prevent automated spam and spam bots.

Features:

+ Random alphanumeric text
+ Supports rotation of text
+ The image width and height dimensions can be set
+ The font size can be adjusted
+ Random monospace fonts are used during generation (anonymous, droidsans, ubuntu)

## Requirements

+ PHP version: 5.2+
+ CakePHP version: 1.3

## Installation

Clone or download the component:

    git clone git://github.com/donovan-duplessis/cakephp-captcha.git

Copy the component and behavior into your framework at:

    cd cakephp-captcha
    cp controllers/components/captcha.php <your-app>/controllers/components/
    cp models/behaviors/captcha.php <your-app>/models/behaviors/

Copy the fonts into your framework at:

    cp -R webroot/fonts <your-app>/webroot/
    chmod 755 <your-app>/webroot/fonts

## Usage

Include Captcha behavior in model: `public $actsAs = array('Captcha');`

Include Captcha component in controller: `public $components = array('Captcha');`

To output the captcha image from controller: `$this->Captcha->generate();`

## Sample Code

Model contact.php

    <?php
    class Contact extends AppModel {
        public $actsAs = array(
            'Captcha' => array(
                'field' => 'captcha',
                'error' => 'Captcha code entered invalid'
            )
        );
    }
    ?>

Controller contacts_controller.php

    <?php
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

Route config/routes.php

    Router::connect('/img/captcha.jpg', array('controller' => 'contacts', 'action' => 'captcha'));

View contacts/index.ctp

    <?php
        echo $this->Form->create('Contact');
        echo $this->Html->image('captcha.jpg', array('style' => 'padding: 0.5%;'));
        echo $this->Form->input('captcha');
        echo $this->Form->end('Send');
    ?>

## License

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.

## Copyright

Copyright (C) Donovan du Plessis, donodp@gmail.com

## Changelog

### 1.1

* Rename __uniqueCode method to __randomCode
* Use characters configuration parameter for length in __randomCode method
* Add characters parameter to default configuration to specify number of characters to display in image

### 1.0

* Initial version
