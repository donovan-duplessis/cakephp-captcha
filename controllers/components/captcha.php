<?php
/**
 * Captcha Component
 *
 * Component which generates a captcha image containing random texts
 *
 * PHP version 5 and CakePHP version 1.3
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category    Component
 * @version     1.1
 * @author      Donovan du Plessis <donodp@gmail.com>
 * @copyright   Copyright (C) Donovan du Plessis
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Version history
 *
 * 2012-03-29  DdP  Initial version
 * 2012-03-30  DdP  - Rename __uniqueCode method to __randomCode
 *                  - Add characters parameter to default configuration to
 *                    specify number of characters to display in image.
 *                  - Use characters configuration parameter for length in
 *                    __randomCode method.
 *
 */
class CaptchaComponent extends Object {

    /**
     * Other components used by this component
     *
     * @var array
     * @access public
     */
    public $components = array('Session');

    /**
     * Component configuration settings
     *
     * @var array
     * @access public
     */
    public $settings = array();

    /**
     * Default values to be merged with settings
     *
     * @var array
     * @access private
     */
    private $__defaults = array(
        'width'      => 120,
        'height'     => 60,
        'rotate'     => false,
        'fontSize'   => 22,
        'characters' => 6,
        'sessionKey' => 'Captcha.code'
    );

    /**
     * Default monospaced fonts available
     *
     * The font files (.ttf) are stored in app/webroot/fonts
     *
     * @var array
     * @access private
     */
    private $__fontTypes = array('anonymous', 'droidsans', 'ubuntu');

    /**
     * Called before the controller beforeFilter method.  Merge passed settings
     * array with the default settings.
     *
     * @param object $controller Controller instance for the request
     * @param array $settings Settings to set on the component
     * @access public
     * @return void
     */
    public function initialize(&$controller, $settings = array()) {
        $this->settings = array_merge($this->__defaults, $settings);
    }

    /**
     * Generate random alphanumeric code to specified character length
     *
     * @access private
     * @return string The generated code
     */
    private function __randomCode() {
        $valid = 'abcdefghijklmnpqrstuvwxyz123456789';
        return substr(str_shuffle($valid), 0, $this->settings['characters']);
    }

    /**
     * Generate and output the random captcha code image according to specified
     * settings and store the image text value in the session.
     *
     * @access public
     * @return void
     */
    public function generate() {
        $text = $this->__randomCode();

        $width  = (int) $this->settings['width'];
        $height = (int) $this->settings['height'];

        $image = imagecreatetruecolor($width, $height);

        $bkgColour = imagecolorallocate($image, 238,239,239);
        $borColour = imagecolorallocate($image, 208,208,208);
        $txtColour = imagecolorallocate($image, 96, 96, 96);

        imagefilledrectangle($image, 0, 0, $width, $height, $bkgColour);
        imagerectangle($image, 0, 0, $width-1, $height - 1, $borColour);

        $noiseColour = imagecolorallocate($image, 205, 205, 193);

        // Add random circle noise
        for ($i = 0; $i < ($width * $height) / 3; $i++) {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height),
                mt_rand(0,3), mt_rand(0,3), $noiseColour);
        }

        // Add random rectangle noise
        for ($i = 0; $i < ($width + $height) / 5; $i++) {
            imagerectangle($image, mt_rand(0,$width), mt_rand(0,$height),
                mt_rand(0,$width), mt_rand(0,$height), $noiseColour);
        }

        // Randomize font selection
        $font = sprintf("fonts%s%s.ttf", DIRECTORY_SEPARATOR,
            $this->__fontTypes[array_rand($this->__fontTypes)]
        );

        // If specified, rotate text
        $angle = 0;
        if($this->settings['rotate']) {
            $angle = rand(-15, 15);
        }

        $box = imagettfbbox($this->settings['fontSize'], $angle, $font, $text);
        $x = ($width  - $box[4]) / 2;
        $y = ($height - $box[5]) / 2;

        imagettftext($image, $this->settings['fontSize'], $angle, $x, $y,
            $txtColour, $font, $text);

        header("Content-type: image/jpeg");
        imagejpeg($image);
        imagedestroy ($image);

        $this->Session->write($this->settings['sessionKey'], $text);
    }

    /**
     * Get captcha code stored in Session
     *
     * @access public
     * @return string The generated captcha code text
     */
    public function getCode()   {
        return $this->Session->read($this->settings['sessionKey']);
    }

}
?>
