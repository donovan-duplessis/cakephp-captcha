<?php
/**
 * Contact Model
 *
 * Demonstrate Captcha validation via Behavior
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
     * Extend model with Captcha Behavior
     *
     * @var array
     * @access public
     */
    public $actsAs = array(
        'Captcha' => array(
            'field' => 'captcha',
            'error' => 'Captcha code entered invalid'
        )
    );

}
?>
