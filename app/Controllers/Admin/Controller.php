<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller as MainController;
use App\Services\Roles;

class Controller extends MainController
{
    public function __construct()
    {
        parent::__construct();

        if(!$this->auth->hasRole(Roles::ADMIN)) {
            abort(404);
        }
    }
}