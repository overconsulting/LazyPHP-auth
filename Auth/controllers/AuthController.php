<?php
/**
 * File Auth\controllers\AuthController.php
 *
 * @category System
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */

namespace Auth\controllers;

use System\Controller;
use System\Session;
use System\Query;
use System\Router;
use System\Password;

/**
 * Auth controller
 *
 * @category System
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */
class AuthController extends Controller
{
    /**
     * @var string
     */
    public $tableName = 'users';

    /**
     * @var string
     */
    public $sessionKey = 'current_user';

    /**
     * @var string
     */
    public $idField = 'email';

    /**
     * @var string
     */
    public $passwordField = 'password';

    /**
     * @var string
     */
    public $layout = 'login';

    /**
     * @var string
     */
    public $loginPage = 'auth_login';

    /**
     * @var string
     */
    public $signupURL = 'auth_signup';

    /**
     * @var string
     */
    public $pageTitle = 'Connexion au service';

    /**
     * @var string
     */
    public $afterLoginPage = 'user_index';

    /**
     * @var string
     */
    public $afterLogoutPage = '';

    /**
     * @var string
     */
    public $model  = 'Auth\\models\\User';

    public function signupAction()
    {
        $class = $this->model;
        $user = new $class();

        if (!empty($this->request->post)) {
            $user->setData($this->request->post);

            if ($user->valid()) {
                // $password = Password::generatePassword();
                $password = $user->password;
                $cryptedPassword = Password::crypt($password);
                $user->password = $cryptedPassword;

                $user->email_verification_code = Password::generateToken();
                $user->email_verification_date = date('Y-m-d H:i:s');
                $user->active = 0;

                if ($user->create((array)$user)) {
                    Session::addFlash('Compte créé', 'success');
                    $this->redirect($this->afterSignupPage);
                } else {
                    Session::addFlash('Erreur insertion base de données', 'danger');
                };
            } else {
                Session::addFlash('Erreur(s) dans le formulaire', 'danger');
            }
        }

        $this->render('signup', array(
            'id' => 0,
            'user' => $user,
            'pageTitle' => $this->pageTitle,
            'formAction' => Router::url($this->signupURL)
        ));
    }

    public function loginAction()
    {
        $errors = array();
        $post = $this->request->post;
        
        if (!empty($post) && isset($post[$this->idField]) && isset($post[$this->passwordField])) {
            $id = trim($post[$this->idField]);
            $password = trim($post[$this->passwordField]);

            if ($id == '') {
                $errors[$this->idField] = 'Identifiant obligatoire';
            } else if (!filter_var($id, FILTER_VALIDATE_EMAIL)) {
                $errors[$this->idField] = 'Email invlaide';
            }

            if ($password == '') {
                $errors[$this->passwordField] = 'Mot de passe obligatoire';
            }

            if (empty($errors)) {
                $query = new Query();
                $query->select('*');
                $query->where($this->idField.' = :idField');
                $query->from($this->tableName);
                $res = $query->executeAndFetch(array('idField' => $id));

                if ($res && Password::check($password, $res->password)) {
                    $class = $this->model;
                    $user = $class::findById($res->id);
                    Session::set($this->sessionKey, $user);
                    $this->redirect($this->afterLoginPage);
                } else {
                    Session::addFlash('Identifiant ou mot de passe incorrect', 'danger');
                }
            }
        }

        $params = array(
            'pageTitle'     => $this->pageTitle,
            'formAction'    => Router::url($this->loginPage),
            'signupURL'     => Router::url($this->signupURL),
            'errors'        => $errors
        );

        if (isset($id)) {
            $params[$this->idField] = $id;
        }

        $this->render('login', $params);
    }

    public function logoutAction()
    {
        Session::remove($this->sessionKey);
        $this->redirect($this->afterLogoutPage);
    }
}
