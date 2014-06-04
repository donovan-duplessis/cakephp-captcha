<?php
/**
 * Contacts Controller
 *
 * Demonstrate use of the Captcha Component
 *
 * PHP version 5 and CakePHP version 2.0+
 *
 * @category Controller
 * @author   Donovan du Plessis <donovan@binarytrooper.com>
 */
class ContactsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Contacts';

    /**
     * Components
     *
     * @var array
     * @access public
     */
    public $components = array(
        'Captcha' => array(
            'rotate' => true
        ),
        'RequestHandler'
    );

    /**
     * Helpers
     *
     * @var array
     * @access public
     */
    public $helpers = array(
        'Js' => array('Jquery')
    );

    /**
     * Captcha field definitions
     *
     * @var array
     * @access public
     */
    public $captchas = array('captcha', 'captcha-2');

    /**
     * Generate and render captcha image
     *
     * @access public
     * @return void
     */
    public function captcha() {
        $this->autoRender = false;
        $captcha = basename($this->params['url']['url'], '.jpg');
        $this->Captcha->generate($captcha);
    }

    /**
     * Display contact form containing captcha image
     *
     * @access public
     * @return void
     */
    public function index() {
        if ($this->RequestHandler->isPost()) {
            foreach($this->captchas as $field) {
                $this->Contact->setCaptcha($field,
                    $this->Captcha->getCode($field));
            };
            $this->Contact->set($this->request->data);
            if ($this->Contact->validates()) {
                $this->Session->setFlash('Captcha codes validated successfully',
                    'flash_good');
            }
        }
        $this->set('captcha_fields', $this->captchas);
    }

}
?>
