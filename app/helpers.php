<?php

use App\Services\Database;
use App\Services\Roles;
use Delight\Auth\Auth;
use JasonGrimes\Paginator;

function view($path, $parameters = [])
{
    global $container;
    $plates = $container->get('plates');
    echo $plates->render($path, $parameters);
}

function back()
{
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

function redirect($path)
{
    header("Location: $path");
    exit;
}
function abort($type)
{
    switch ($type) {
        case 404:
            $view = components(\League\Plates\Engine::class);
            echo $view->render('errors/404');exit;
        break;
    }
}

function config($field)
{
    $config = require '../app/config.php';
    return array_get($config, $field);
}

function auth()
{
    global $container;
    return $container->get(Auth::class);
}

function isAdmin()
{
//    return auth()->hasRole()
}

function getRole($key)
{
    return Roles::getRole($key);
}

function getImage($image) {
    return (new \App\Services\ImageManager())->getImage($image);
}

function paginate($count, $page, $perPage, $url)
{
    $totalItems = $count;
    $itemsPerPage = $perPage;
    $currentPage = $page;
    $urlPattern = $url;

    $paginator =  new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
    return $paginator;

}

function paginator($paginator)
{
    include config('views_path') . 'partials/pagination.php';
}

function uploadedDate($timestamp)
{
    return date('d.m.Y', $timestamp);
}

function getCategory($id)
{
    global $container;
    $queryFactory = $container->get('Aura\SqlQuery\QueryFactory');
    $pdo = $container->get('PDO');
    $database = new Database($pdo, $queryFactory);
    return $database->find('categories', $id);
}

function getAllCategories()
{
    global $container;
    $queryFactory = $container->get('Aura\SqlQuery\QueryFactory');
    $pdo = $container->get('PDO');
    $database = new Database($pdo, $queryFactory);
    return $database->all('categories', 'none');
}

function components($name)
{
    global $container;
    return $container->get($name);
}