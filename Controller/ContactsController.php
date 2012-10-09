<?php
/**
 * Contacts Controller
 *
 * Demonstrate use of the Captcha Component
 *
 * PHP version 5 and CakePHP version 1.3
 *
 * @category Controller
 * @author   Donovan du Plessis <donodp@gmail.com>
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
     * Generate and render captcha image
     *
     * @access public
     * @return void
     */
    public function captcha()  {
        $this->autoRender = false;
        $this->Captcha->generate();
    }

    /**
     * Display contact form containing captcha image
     *
     * @access public
     * @return void
     */
    public function index() {
        if ($this->RequestHandler->isPost()) {
            $this->Contact->setCaptcha($this->Captcha->getCode());
            $this->Contact->set($this->request->data);
            if ($this->Contact->validates()) {
                $this->Session->setFlash('Captcha code validated successfully',
                    'flash_good');
            }
        }
    }

}
?>
