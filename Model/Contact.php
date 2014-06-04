<?php
/**
 * Contact Model
 *
 * Demonstrate Captcha validation via Behavior
 *
 * PHP version 5 and CakePHP version 2.0+
 *
 * @category Model
 * @author   Donovan du Plessis <donovan@binarytrooper.com>
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
     * Extend model with Captcha Behavior
     *
     * @var array
     * @access public
     */
    public $actsAs = array(
        'Captcha' => array(
            'field' => array('captcha', 'captcha-2'),
            'error' => 'Captcha code entered invalid'
        )
    );

}
?>
