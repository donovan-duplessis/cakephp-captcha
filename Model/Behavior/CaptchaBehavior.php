<?php
/**
 * Captcha Behavior
 *
 * Behavior which handles Captha verification
 *
 * PHP version 5 and CakePHP version 2.0+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category    Behavior
 * @version     1.2
 * @author      Donovan du Plessis <donodp@gmail.com>
 * @copyright   Copyright (C) Donovan du Plessis
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Version history
 *
 * 2012-04-18  DdP  Initial version
 * 2012-04-19  DdP  Extract default configuration settings into class variable
 * 2012-10-09  ALR  Change class to extend ModelBehavior (2.0 compliant)
 *
 */
App::uses('ModelBehavior', 'Model');

class CaptchaBehavior extends ModelBehavior
{

    /**
     * Behavior configuration settings
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
        'field' => 'captcha',
        'error' => 'Captcha code value incorrect'
    );

    /**
     * Core validation rules set on model
     *
     * @var array
     * @access private
     */
    private $__rules = array();

    /**
     * Store the captcha text value
     *
     * @var string
     * @access private
     */
    private $__captcha = null;

    /**
     * (non-PHPdoc)
     * @see ModelBehavior::setup()
     */
    public function setup(Model $model, $config = array()) {
        if (!isset($this->settings[$model->alias])) {
            $this->settings[$model->alias] = $this->__defaults;
        }

        $this->settings[$model->alias] = array_merge(
            $this->settings[$model->alias], (array) $config);

        $this->__rules[$model->alias] = $model->validate;
    }

    /**
     * (non-PHPdoc)
     * @see ModelBehavior::beforeValidate()
     */
    public function beforeValidate(Model $model) {
        $validator = array(
            'rule' => array('verifyCaptcha'),
            'message' => $this->settings[$model->alias]['error']
        );

        $model->validate = array_merge(
            $this->__rules[$model->alias],
            array(
                $this->settings[$model->alias]['field'] => $validator
            )
        );
    }

    /**
     * Custom validation rule to check if the entered captcha value is
     * equal to the stored captcha value.
     *
     * @param object $model The model reference
     * @param array $check The array containing captcha field value
     * @access public
     * @return boolean True if the captcha values match
     */
    public function verifyCaptcha(Model $model, $check) {
        return array_shift($check) == $this->__captcha;
    }

    /**
     * Store captcha value (from session via controller)
     *
     * @param object $model The model reference
     * @param string $value The captcha value
     * @access public
     * @return void
     */
    public function setCaptcha(Model $model, $captcha) {
        $this->__captcha = $captcha;
    }

}
