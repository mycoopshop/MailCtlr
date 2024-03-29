<?php

require_once __BASE__.'/module/userrole/model/User.php';

class LoginController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $app->render_view();
    }

    ##

    public function loginAction()
    {
        $app = App::getInstance();
        $username = $_POST['username'];
        $password = $_POST['password'];
        $redirect = $_POST['redirect'];
        $remember = @$_POST['remember'];
        if (User::canUserLogin($username, $password)) {
            $user = User::fetchByLogin($username);
            $app->setSessionUser($user->id, $user->username, [$user->role], $user->nome.' '.$user->cognome);
            if ($redirect) {
                $app->Redirect($redirect);
            } else {
                $app->Redirect(__HOME__);
            }
        } else {
            $app->Redirect(__HOME__.'/login', [
                'alert' => _('Username not valid'),
            ]);
        }
    }

    ##

    public function logoutAction()
    {
        $app = App::getInstance();
        $app->setSessionUser('-1', 'undefined', ['public']);
        $app->Redirect(__HOME__.'/login');
    }
}
