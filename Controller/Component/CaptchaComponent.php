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
 * @version     1.7
 * @author      Donovan du Plessis <donovan@binarytrooper.com>
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
 * 2013-06-19  DdP  - Add initialize method to access controller response
 *                  - Set response type and body via response object
 * 2014-06-04  DdP  - Add sessionPrefix default configuration parameter
 *                  - Add support for multiple captcha instantiations
 * 2014-07-23  YLK  Capture the data from imagejpeg() in a variable using
 *                  ob_start() and ob_get_clean() and then set it as the
 *                  response body.
 * 2014-07-25  DdP  - Add support for theme colour configurations
 *                  - Add support for multiple types: alpha|math
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
     * Response object
     *
     * @var CakeResponse
     */
    public $response;

    /**
     * Default theme colour profiles
     *
     * @var array
     */
    private $__themes = array(
        'default' => array(
            'bkgColor'   => array(238, 239, 239),
            'txtColor'   => array(32, 32, 32),
            'noiseColor' => array(160, 160, 160),
        ),
        'green' => array(
            'bkgColor'   => array(0, 255, 0),
            'txtColor'   => array(255, 255, 255),
            'noiseColor' => array(0, 153, 0)
        ),
        'red' => array(
            'bkgColor'   => array(255, 153, 153),
            'txtColor'   => array(255, 255, 255),
            'noiseColor' => array(255, 0, 0)
        ),
        'blue' => array(
            'bkgColor'   => array(0, 128, 255),
            'txtColor'   => array(255, 255, 51),
            'noiseColor' => array(0, 0, 255)
        ));


    /**
     * Default values to be merged with settings
     *
     * @var array
     */
    private $__defaults = array(
        'width'         => 120,
        'height'        => 60,
        'rotate'        => false,
        'fontSize'      => 22,
        'characters'    => 6,
        'sessionPrefix' => 'Captcha',
        'theme'         => 'default',
        'type'          => array('alpha')
    );

    /**
     * Default monospaced fonts available
     *
     * The font files (.ttf) are stored in app/Lib/Fonts
     *
     * @var array
     */
    private $__fontTypes = array('anonymous', 'droidsans', 'ubuntu');

    /**
     * Base arithmetic operators available
     *
     * @var array
     */
    private $__operators = array('+', '-');

    /**
     * Initializes CaptchaComponent for use in the controller
     *
     * @param Controller $controller A reference to the instantiating controller object
     * @return void
     */
    public function initialize(Controller $controller) {
        $this->response = $controller->response;
    }
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
    private function __randomAlpha()
    {
        $valid = 'abcdefghijklmnpqrstuvwxyz123456789';
        return substr(str_shuffle($valid), 0, $this->settings['characters']);
    }

    /**
     * Generate unique session key (by field name) with prefix
     *  e.g. <prefix>.<field>
     *
     * @access private
     * @param string $field The field name to identify each captcha control
     * @return string The generated session key
     */
    private function _sessionKey($field)
    {
        return "{$this->settings['sessionPrefix']}.{$field}";
    }

    /**
     * Generate random arithmetic expression
     *  e.g. number [+|-] number
     *
     * @access private
     * @param integer $minimum The minimum number to generate
     * @param integer $maximum The maximum number to generate
     * @return string The generated expression
     */
    private function __randomArithmetic($minimum = 1, $maximum = 6)
    {
        $numbers = range($minimum, $maximum);
        shuffle($numbers);

        // Pick two random numbers for each side of operator
        list($left, $right) = array_slice($numbers, 0, 2);

        // Get random operator [+|-]
        $operator = $this->__operators[array_rand($this->__operators)];

        // If operation is subtraction (−), and the left value is less than
        // the right value, then swap left and right (postitive results).
        if($operator == '-' && $left < $right) {
            list($left, $right) = array($right, $left);
        }

        return sprintf('%d%s%d', $left, $operator, $right);
    }

    /**
     * Get colour profile for specified theme name
     *  e.g. default|random|green|red|blue
     *
     * @access private
     * @return array The theme colour profile
     */
    private function __getTheme()
    {
        $setting = strtolower($this->settings['theme']);

        if($setting == 'random') {
            $theme = array_rand($this->__themes);
        } else {
            $theme = array_key_exists($setting, $this->__themes) ?
                $setting : 'default';
        }

        return $this->__themes[$theme];
    }

    /**
     * Generate and output the random captcha code image according to specified
     * settings and store the image text value in the session.
     *
     * @access public
     * @param string $field The field name to identify each captcha control
     * @return void
     */
    public function generate($field='captcha')
    {
        // Generate random captcha text for specified captcha type(s),
        // supports: alpha|math.
        $captchaType =
            $this->settings['type'][array_rand($this->settings['type'])];

        if($captchaType == 'math') {
            $text = $this->__randomArithmetic();
            // Calculate result of arithmetic expression; eval() is dangerous
            // but we are in control of input (no user provided data).
            $sessionValue = eval("return($text);");
            $text .= '=?';
        } else  {
            $sessionValue = $text = $this->__randomAlpha();
        }

        $width  = (int) $this->settings['width'];
        $height = (int) $this->settings['height'];

        $image = imagecreatetruecolor($width, $height);

        // Get theme colour profile
        $theme = $this->__getTheme();

        $bkgColour = call_user_func_array('imagecolorallocate',
            array_merge((array) $image, $theme['bkgColor']));
        $txtColour = call_user_func_array('imagecolorallocate',
            array_merge((array) $image, $theme['txtColor']));

        $borColour = imagecolorallocate($image, 208, 208, 208);

        imagefilledrectangle($image, 0, 0, $width, $height, $bkgColour);
        imagerectangle($image, 0, 0, $width-1, $height - 1, $borColour);

        $noiseColour = call_user_func_array('imagecolorallocate',
            array_merge((array) $image, $theme['noiseColor']));

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

        $sessionKey = $this->_sessionKey($field);

        $this->Session->delete($sessionKey);
        $this->Session->write($sessionKey, $sessionValue);

        // Capture the image in a variable
        ob_start();
        imagejpeg($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Set the image as the body of the response
        $this->response->type('jpg');
        $this->response->body($imageData);
        $this->response->disableCache();
    }

    /**
     * Get captcha code stored in Session for specified captcha field
     *
     * @access public
     * @param string $field The field name to identify each captcha control
     * @return string The generated captcha code text
     */
    public function getCode($field='captcha')
    {
        return $this->Session->read($this->_sessionKey($field));
    }

}
