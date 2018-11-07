<?php
/**
 * Created by PhpStorm.
 * User: Апчхи
 * Date: 18.10.2018
 * Time: 23:35
 */

namespace App\Controllers;


use App\Services\Notifications;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Respect\Validation\Validator;

class ResetPasswordController extends Controller
{
    private $notifications;

    public function __construct(Notifications $notifications)
    {
        parent::__construct();
        $this->notifications = $notifications;
    }

    public function showForm()
    {
        echo $this->view->render('auth/password-recovery');
    }

    public function recovery()
    {
        try {
            $this->auth->forgotPassword($_POST['email'], function ($selector, $token) {
                $this->notifications->passwordReset($_POST['email'], $selector, $token);
                flash()->success(['Cсылка на сброс пароля отправлена на почту']);
            });


        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Неправильный email']);
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            flash()->error(['Email не подтверждён']);
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
            //Password reset is disabled
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Слишком много попыток']);
        }

        return back();
    }

    public function showSetForm()
    {
        if ($this->auth->canResetPassword($_GET['selector'], $_GET['token'])) {


            echo $this->view->render('auth/password-set', ['data' => $_GET]);
        }
    }

    public function change()
    {
        $this->validate();

        try {
            $this->auth->resetPassword($_POST['selector'], $_POST['token'], $_POST['password']);

            flash()->success(['Пароль изменён']);
            return redirect('/login');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            flash()->error(['Неверный токен']);
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            flash()->error(['Токен устарел']);
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
           //Password reset is disabled
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Введите пароль']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Слишком много попыток']);
        }

        return back();
    }




    function validate()
    {
        $validator = v::key('password', v::stringType()->notEmpty())
            ->keyValue('password_confirmation', 'equals', 'password');

        try {
            $validator->assert($_POST);
        }

        catch(ValidationException $exception) {
            flash()->error($exception->findMessages([
                'password' => 'Введите пароль.',
                'password_confirmation' => 'Пароли не совпадают.'
            ]));

            return back();
        }
    }
}