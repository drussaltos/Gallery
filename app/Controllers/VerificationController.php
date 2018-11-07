<?php
/**
 * Created by PhpStorm.
 * User: Апчхи
 * Date: 18.10.2018
 * Time: 18:54
 */

namespace App\Controllers;


use Tamtamchik\SimpleFlash\Flash;

class VerificationController extends Controller
{
    private $flash;

    public function __construct(Flash $flash)
    {
        parent::__construct();
        $this->flash = $flash;
    }

    public function showForm()
     {
         echo $this->view->render('auth/verification-form');
     }

     public function verify()
     {
         try {
             $this->auth->confirmEmail($_GET['selector'], $_GET['token']);

             flash()->success(['Email подтверждён']);
         }
         catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
             flash()->error(['Неверный токен']);
         }
         catch (\Delight\Auth\TokenExpiredException $e) {
             flash()->error(['Токен устарел']);
         }
         catch (\Delight\Auth\UserAlreadyExistsException $e) {
             flash()->error(['Email уже существует']);
         }
         catch (\Delight\Auth\TooManyRequestsException $e) {
             flash()->error(['Слишком много попыток']);
         }

         return redirect('/login');
     }
}