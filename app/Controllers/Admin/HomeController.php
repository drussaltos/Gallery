<?php


namespace App\Controllers\Admin;



class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo $this->view->render('admin/index');
    }
}