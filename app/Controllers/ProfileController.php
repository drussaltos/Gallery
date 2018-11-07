<?php


namespace App\Controllers;


use App\Services\Database;
use App\Services\ImageManager;
use App\Services\Notifications;
use Delight\Auth\Auth;
use League\Plates\Engine;

class ProfileController extends Controller
{
    private $notifications;
    private $imageManager;

    public function __construct(Notifications $notifications, ImageManager $imageManager)
    {
        parent::__construct();
        $this->notifications = $notifications;
        $this->imageManager = $imageManager;
    }

    public function showInfo()
    {
        $this->checkLogged();
        $user = $this->database->find('users', $this->auth->getUserId());

        echo $this->view->render('profile/info', compact('user'));
    }

    public function updateInfo()
    {
        try {

            $newEmail = $_POST['email'];
            if ($this->auth->getEmail() != $newEmail) {
                $this->auth->changeEmail($_POST['email'], function ($selector, $token) {
                    $this->notifications->emailWasChanged($_POST['email'], $selector, $token);
                    flash()->success(['На вашу почту ' . $_POST['email'] . ' был отправлен код с подтверждением.']);
                });

                flash()->success(['Изменение вступит в силу, как только новый адрес электронной почты будет подтвержден']);
            }

            $user = $this->database->find('users', $this->auth->getUserId());

            $image = $this->imageManager->uploadImage($_FILES['image'], $user['image']);
            $dimensions = $this->imageManager->getDimensions($image);

            $this->database->update('users', $this->auth->getUserId(), [
                'username'   =>  isset($newUsername) ? $newUsername : $this->auth->getUsername(),
                "image"   =>  $image,
                "dimensions" =>  $dimensions
            ]);
//            else {
//                echo 'Email совпадает';
//            }
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['неверный формат имейла']);
            // invalid email address
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            // email address already exists
            flash()->error(['имейл уже существует']);
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            // account not verified
            flash()->error(['почта не подтверждена']);
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            // not logged in
            flash()->error(['ты не залогинен']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            // too many requests
            flash()->error(['Слишком много попыток']);
        }



        $this->database->update( 'users', $this->auth->getUserId(), [
            'username'   =>  isset($_POST['username']) ? $_POST['username'] : $this->auth->getUsername(),
        ]);
        return back();

    }

    public function showSecurity()
    {
        $this->checkLogged();
       echo $this->view->render('profile/security');
    }

    public function updateSecurity(){
        try {
            $this->auth->changePassword($_POST['password'], $_POST['new_password']);
            flash()->success(['Пароль успешно изменен.']);
            // password has been changed
            $user = $this->database->find('users', $this->auth->getUserId());
            $this->notifications->passwordUpdate($user['email']);
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            // not logged in
            flash()->error(['Залогиньтесь!']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            // invalid password(s)
            flash()->error(['Неправильный пароль!']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            // too many requests
            flash()->error(['лишком много попыток!']);
        }

        return back();
    }

}