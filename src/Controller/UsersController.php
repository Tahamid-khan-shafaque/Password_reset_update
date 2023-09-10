<?php
declare(strict_types=1);

namespace App\Controller;
use App\Controller\AppController;
use Cake\Routing\Router;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
  


    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Posts'],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function login(){
        if($this->request->is('post')){
            $user = $this->Auth->identify();
            if($user){
                $this->Auth->setUser($user);
                return $this->redirect(['controller' => 'posts']);
            }
            // Bad Login
            $this->Flash->error('Incorrect Login');
        }
    }
    //logout
    public function logout(){
        $this->Flash->success('You are logged out');
        return $this->redirect($this->Auth->logout());
   }
   //given*
   

   public function forgotPassword() {
    $this->viewBuilder()->setHelpers(['Url']);
    if ($this->request->is('post')) {
        // Get the email address from the form
        $email = $this->request->getData('email'); 

        // Load the User model
        $this->loadModel('Users'); 

        // Find the user by email
        $user = $this->Users->find()
            ->where(['email' => $email]) 
            ->first();

        if ($user) {
            // Generate a token and save it to the database
            $token = \Cake\Utility\Security::hash(\Cake\Utility\Text::uuid(), 'sha256', true);
            $user->token = $token;
            
            if ($this->Users->save($user)) {
                // Send an email with the reset password link
                $resetLink = $this->Url->build([
                    'controller' => 'Users',
                    'action' => 'reset_password',
                    $token
                ], ['fullBase' => true]);

                $email = new \Cake\Mailer\Email();
                $email->setFrom('your_email@example.com')
                      ->setTo($user->email)
                      ->setSubject('Reset your password')
                      ->send('Click on this link to reset your password: ' . $resetLink);

                $this->Flash->success('An email has been sent with instructions to reset your password.');
                return $this->redirect(['action' => 'login']);
            } else {
                // Handle the case where saving the token fails
            }
        } else {
            $this->Flash->error('Email not found.');
        }
    }
    $this->render('forgot_password');
}



   //given end****
   //register
   public function register(){
    $user = $this->Users->newEntity($this->request->getData());
    if($this->request->is('post')){
        $user = $this->Users->patchEntity($user, $this->request->getData());
        if($this->Users->save($user)){
            $this->Flash->success('You are registered and can login');
            return $this->redirect(['action' => 'login']);
        } else {
            $this->Flash->error('You are not registered');
        }
    }
    $this->set(compact('user'));
    $this->set('_serialzie', ['user']);
}

public function beforeFilter(\Cake\Event\EventInterface $event){
    $this->Auth->allow(['register']);
}
}



