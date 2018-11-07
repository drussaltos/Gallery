<?php


namespace App\Controllers;


use App\Services\Notifications;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Tamtamchik\SimpleFlash\Flash;
use Swift_Mailer;
use Swift_Message;



class RegisterController extends Controller
{
    private $flash;
    private $notifications;


    public function __construct(Flash $flash, Notifications $notifications)
    {
        parent::__construct();
        $this->flash = $flash;
        $this->notifications = $notifications;
    }

    public function setTemplate()
    {
        $this->flash->setTemplate($this->template);
    }

    public function showForm(){


        echo $this->view->render('auth/register');
    }

    public function register()
    {
        $this->validate();
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
                // send `$selector` and `$token` to the user (e.g. via email)

                $this->notifications->emailWasChanged($_POST['email'], $selector, $token);
            });

//            $this->database->update('users', $userId, ['roles_mask' =>  Roles::USER]);
            flash()->success(['На вашу почту ' . $_POST['email'] . ' был отправлен код с подтверждением.']);
            return redirect('/login');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Неправильный email']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Неправильный пароль']);
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error(['Пользователь уже существует']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Слишком много раз пытаетесь зарегистрироваться']);
        }

        return redirect('/register');

    }

    function validate()
    {
        $validator = v::key('username', v::stringType()->notEmpty())
            ->key('email', v::email())
            ->key('password', v::stringType()->notEmpty())
            ->keyValue('password_confirmation', 'equals', 'password')
            ->key('terms', v::trueVal());

        try {
            $validator->assert($_POST);
        }

        catch(ValidationException $exception) {
            flash()->error($exception->findMessages([
                'username' => 'Введите имя.',
                'email' => 'Неверный формат e-mail.',
                'password' => 'Введите пароль.',
                'password_confirmation' => 'Пароли не совпадают.',
                'terms' => 'Вы должны согласится с правилами.'
            ]));

            return redirect('register');
        }
    }




}