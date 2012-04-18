# Captcha Component for CakePHP 1.3

Generates a image with random alphanumeric characters which requires a human to validate.  This is to prevent automated spam and spam bots.

Features:

+ Random alphanumeric characters
+ The image width and height dimensions can be set
+ The font size can be adjusted
+ Random monospace fonts are used during generation (anonymous, droidsans, ubuntu)

## Requirements

+ PHP version: 5.2+
+ CakePHP version: 1.3

## Installation

Clone or download the component:

        git clone git://github.com/donovan-duplessis/cakephp-captcha.git

Copy the component into your framework at:

        cd cakephp-captcha
        cp controllers/components/captcha.php <your-app>/controllers/components/

## Usage

In the controller that requires a captcha implementation, include `public $components = array('Captcha');`.

## Sample Code


## License

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.

## Copyright

Copyright (C) Donovan du Plessis, donodp@gmail.com

## Changelog

### 1.1

* Rename __uniqueCode method to __randomCode
* Add characters parameter to default configuration to specify number of characters to display in image
* Use characters configuration parameter for length in __randomCode method

### 1.0

* Initial version
