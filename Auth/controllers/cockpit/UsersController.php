<?php

namespace Auth\controllers\cockpit;

use app\controllers\cockpit\CockpitController;
use Core\Router;
use Core\Session;
use Core\Password;

use Auth\models\User;
use Auth\models\Group;
use Multisite\models\Site;

class UsersController extends CockpitController
{
    /*
     * @var Auth\models\User
     */
    public $user = null;

    public function indexAction()
    {
        if ($this->current_administrator->site_id !== null) {
            $where = 'site_id = '.$this->current_administrator->site_id;
        } else {
            $where = '';
        }
        $users = User::findAll($where);

        $this->render('auth::users::index', array(
            'pageTitle' => '<i class="fa fa-users"></i> Utilisateurs',
            'users' => $users
        ));
    }

    public function newAction()
    {
        if ($this->user === null) {
            $this->user = new User();
        }

        $groupOptions = Group::getOptions();
        $siteOptions = Site::getOptions();

        $this->render('auth::users::edit', array(
            'id' => 0,
            'user' => $this->user,
            'pageTitle' => 'Nouvel utilisateur',
            'groupOptions' => $groupOptions,
            'siteOptions' => $siteOptions,
            'formAction' => url('cockpit_auth_users_create')
        ));
    }

    public function editAction($id)
    {
        if ($this->user === null) {
            $this->user = User::findById($id);
        }

        $groupOptions = Group::getOptions();
        $siteOptions = Site::getOptions();

        $this->render('auth::users::edit', array(
            'id' => $id,
            'user' => $this->user,
            'pageTitle' => 'Modification utilisateur n°'.$id,
            'groupOptions' => $groupOptions,
            'siteOptions' => $siteOptions,
            'formAction' => Router::url('cockpit_auth_users_update_'.$id)
        ));
    }

    public function createAction()
    {
        $this->user = new User();
        $this->user->setData($this->request->post);

        if ($this->user->valid()) {
            $password = Password::generatePassword();
            $cryptedPassword = Password::crypt($password);
            $this->user->password = $cryptedPassword;

            $this->user->email_verification_code = Password::generateToken();
            $this->user->email_verification_date = date('Y-m-d H:i:s');
            $this->user->active = 0;

            if ($this->user->create((array)$this->user)) {
                Session::addFlash('Utilisateur ajouté', 'success');
                $this->redirect('cockpit_auth_users');
            } else {
                Session::addFlash('Erreur insertion base de données', 'danger');
            };
        } else {
            Session::addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        $this->user = User::findById($id);
        $this->user->setData($this->request->post);

        if ($this->user->valid()) {
            $newPassword = trim($this->request->post['newPassword']);
            if ($newPassword != '') {
                if (Password::validPassword($newPassword)) {
                    $this->user->password = Password::crypt($newPassword);
                } else {
                    $this->user->errors['newPassword'] = 'Mot de passe invalide';
                }
            }

            if (empty($this->user->errors)) {
                if ($this->user->update((array)$this->user)) {
                    Session::addFlash('Utilisateur modifié', 'success');
                    $this->redirect('cockpit_auth_users');
                } else {
                    Session::addFlash('Erreur mise à jour base de données', 'danger');
                }
            } else {
                Session::addFlash('Erreur(s) dans le formulaire', 'danger');
            }
        }

        $this->editAction($id);
    }

    public function deleteAction($id)
    {
        $user = User::findById($id);
        $user->delete();
        Session::addFlash('Utilisateur supprimé', 'success');
        $this->redirect('cockpit_auth_users');
    }
}
