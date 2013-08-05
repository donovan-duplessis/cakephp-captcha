# Captcha implementation for CakePHP 2.2

Generates an image with random alphanumeric characters which requires a human to validate.  This is to prevent automated spam and spam bots.

Features:

+ Random alphanumeric text
+ Supports rotation of text
+ The image width and height dimensions can be set
+ The font size can be adjusted
+ Random monospace fonts are used during generation (anonymous, droidsans, ubuntu)

## Website

[captcha.baselocker.com](http://captcha.baselocker.com)

## Requirements

+ PHP version: 5.2+
+ CakePHP version: 2.0+

## Installation

Clone or download the component:

    git clone git://github.com/donovan-duplessis/cakephp-captcha.git

Copy the component and behavior into your framework at:

    cd cakephp-captcha
    cp Controller/Component/CaptchaComponent.php <your-app>/app/Controller/Component/
    cp Model/Behavior/CaptchaBehavior.php <your-app>/app/Model/Behavior/

Copy the fonts into your framework at:

    cp -R Lib/Fonts <your-app>/app/Lib/
    chmod 755 <your-app>/app/Lib/Fonts

## Usage

Include Captcha behavior in model:<br/>

    public $actsAs = array('Captcha');

Include Captcha component in controller:

    public $components = array('Captcha');

To output the captcha image from controller:

    $this->Captcha->generate();

## Reload image

Refer to sample code (mostly in contacts index view) which refreshes the captcha image via jQuery.

## Authentication

If you are using the authentication component, ensure that the captcha action (that generates the image) is granted access

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

Copyright (C) Donovan du Plessis, donovan@binarytrooper.com

## Contributors

Adriano Luís Rocha, <driflash@gmail.com>

## Changelog

##### 1.5 [Aug 05, 2013]
* Add reload captcha image implementation to contacts sample code

##### 1.4 [Jun 19, 2013]
* Add initialize method to component to set correct image response type+body
* Refactor readme document and script comments

##### 1.3 [Oct 25, 2012]
* Set font path to Lib/Fonts (Adriano Luís Rocha)
* Access Model reference correctly in Behavior (Adriano Luís Rocha)

##### 1.2 [Oct 17, 2012]
* Set component and behavior to framework 2.0 compliant (Adriano Luís Rocha)

##### 1.1 [Apr 18, 2012]
* Add character limit configuration to component
* Initial default configuration settings in Behavior
* Refactor code and readme document

##### 1.0 [Mar 29, 2012]
* Initial Version
