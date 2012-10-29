<?php
/**
 * Captcha Component
 *
 * Component which generates a captcha image containing random texts
 *
 * PHP version 5 and CakePHP version 2.0+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category    Component
 * @version     1.3
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
 * 2012-10-09  ALR  Change class to extend Component (2.0 compliant)
 * 2012-10-25  ALR  Modify font directory path to <app>/Lib/Fonts
 *
 */
App::uses('Component', 'Controller');

class CaptchaComponent extends Component
{

    /**
     * Other Components this component uses.
     *
     * @var array
     */
    public $components = array('Session');

    /**
     * Settings for this Component
     *
     * @var array
     */
    public $settings = array();

    /**
     * Default values to be merged with settings
     *
     * @var array
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
     */
    private $__fontTypes = array('anonymous', 'droidsans', 'ubuntu');

    /**
     * Constructor
     *
     * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
     * @param array $settings Array of configuration settings.
     */
    public function __construct(ComponentCollection $collection, $settings = array())
    {
        parent::__construct($collection, array_merge($this->__defaults, $settings));
    }

    /**
     * Generate random alphanumeric code to specified character length
     *
     * @access private
     * @return string The generated code
     */
    private function __randomCode()
    {
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
    public function generate()
    {
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
        for ($i = 0; $i < ($width * $height) / 3; $i++)
        {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height),
                    mt_rand(0,3), mt_rand(0,3), $noiseColour);
        }

        // Add random rectangle noise
        for ($i = 0; $i < ($width + $height) / 5; $i++)
        {
            imagerectangle($image, mt_rand(0,$width), mt_rand(0,$height),
                    mt_rand(0,$width), mt_rand(0,$height), $noiseColour);
        }

        // Gets full path to fonts dir
        $fontsPath = dirname(dirname(dirname(__FILE__))) . DS . 'Lib' . DS . 'Fonts';

        // Randomize font selection
        $fontName = "{$this->__fontTypes[array_rand($this->__fontTypes)]}.ttf";

        $font = $fontsPath . DS . $fontName;

        // If specified, rotate text
        $angle = 0;
        if($this->settings['rotate'])
        {
            $angle = rand(-15, 15);
        }

        $box = imagettfbbox($this->settings['fontSize'], $angle, $font, $text);
        $x = ($width  - $box[4]) / 2;
        $y = ($height - $box[5]) / 2;

        imagettftext($image, $this->settings['fontSize'], $angle, $x, $y,
                $txtColour, $font, $text);

        $this->Session->delete($this->settings['sessionKey']);
        $this->Session->write($this->settings['sessionKey'], $text);

        header("Content-type: image/jpeg");
        imagejpeg($image);
        imagedestroy ($image);
    }

    /**
     * Get captcha code stored in Session
     *
     * @access public
     * @return string The generated captcha code text
     */
    public function getCode()
    {
        return $this->Session->read($this->settings['sessionKey']);
    }

}
