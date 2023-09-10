<?php
declare(strict_types=1);


namespace App\Controller;

use Cake\Controller\Controller;


class AppController extends Controller
{
    protected $viewVars = [];
  
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login',
            ]
        ]);
    }
    //given
    public function send_mail($email_data = null)
    {    
            //echo "<pre>";print_r($email_data);die;
            //echo WWW_ROOT;die;
        $email         = new CakeEmail('default');
        $email_to      = $email_data['to'];
        $email_msg     = $email_data['body'];
        $email_subject = $email_data['subject'];
        
        $email->to($email_to);
        $email->subject($email_subject);
        $mail_status = @$email->send($email_msg);
     
        if (!$mail_status) {
            return FALSE;
        }
        return TRUE;
    }
    //given end
    //second
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        if (is_array($this->viewVars) && !array_key_exists('_serialize', $this->viewVars) &&
    in_array($this->response->getType(), ['application/json', 'application/xml'])
) {
    $this->set('_serialize', true);
}

// Login Check
if ($this->getRequest()->getSession()->read('Auth.User')) {
    $this->set('loggedIn', true);
} else {
    $this->set('loggedIn', false);
}
    }
}


