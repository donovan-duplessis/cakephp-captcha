<?php
/**
 * Contact Model
 *
 * Demonstrate Captcha validation
 *
 * PHP version 5 and CakePHP version 1.3
 *
 * @category Model
 * @author   Donovan du Plessis <donodp@gmail.com>
 */
class Contact extends AppModel {

    /**
     * No database table association required
     *
     * @var mixed
     * @access public
     */
    public $useTable = false;

    /**
     * Provide custom field validations for the Contact model
     *
     * @var array
     * @access public
     */
    public $validate = array(
        'captcha' => array(
            'rule' => array('verifyCaptcha'),
            'message' => 'Captcha code value incorrect'
        )
    );

    /**
     * Store the captcha text value
     *
     * @var string
     * @access private
     */
    private $__captcha = null;

    /**
     * Custom validation rule to check if the entered captcha value is
     * equal to the stored captcha value.
     *
     * @param array $check The array containing captcha field value
     * @access public
     * @return boolean True if the captcha values match
     */
    public function verifyCaptcha($check) {
        return array_shift($check) == $this->__captcha;
    }

    /**
     * Store captcha value (from session via controller)
     *
     * @param string $value The captcha value
     * @access public
     * @return void
     */
    public function setCaptcha($value) {
        $this->__captcha = $value;
    }

}
?>
