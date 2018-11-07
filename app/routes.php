<?php


use Aura\SqlQuery\QueryFactory;
use DI\ContainerBuilder;
use Delight\Auth\Auth;
use FastRoute\RouteCollector;
use League\Plates\Engine;

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions([
    Engine::class => function(){
        return new Engine('../app/Views');
    },

    Swift_Mailer::class => function() {
        $transport = (new Swift_SmtpTransport(
            config('mail.smtp'),
            config('mail.port'),
            config('mail.encryption')
        ))
            ->setUsername(config('mail.email'))
            ->setPassword(config('mail.password'));
        return new Swift_Mailer($transport);
    },

    PDO::class => function(){
        $driver = config('database.driver');
        $host = config('database.host');
        $database_name = config('database.database_name');
        $username = config('database.username');
        $password = config('database.password');

        return new PDO("$driver:host=$host;dbname=$database_name", $username, $password);
    },

    Delight\Auth\Auth::class   =>  function($container) {
        return new Auth($container->get('PDO'));
    },

    QueryFactory::class  =>  function() {
        return new QueryFactory('mysql');
    }
]);
session_start();

$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
$r->addRoute('GET', '/', ["App\controllers\HomeController", "index"]);
$r->addRoute('GET', '/photo/{id:\d+}', ["App\controllers\HomeController", "photo"]);
$r->addRoute('GET', '/category/{id:\d+}', ["App\controllers\HomeController", "category"]);
$r->addRoute('GET', '/user/{id:\d+}', ["App\controllers\HomeController", "user"]);


$r->addRoute('GET', '/login', ["App\controllers\LoginController", "showForm"]);
$r->addRoute('GET', '/register', ["App\controllers\RegisterController", "showForm"]);
$r->addRoute('GET', '/email-verification', ["App\controllers\VerificationController", "showForm"]);
$r->addRoute('GET', '/verify_email', ["App\controllers\VerificationController", "verify"]);
$r->addRoute('GET', '/password-recovery', ["App\controllers\ResetPasswordController", "showForm"]);
$r->addRoute('POST', '/password-recovery', ["App\controllers\ResetPasswordController", "recovery"]);
$r->addRoute('GET', '/password-recovery/form', ["App\controllers\ResetPasswordController", "showSetForm"]);
$r->addRoute('POST', '/password-recovery/change', ["App\controllers\ResetPasswordController", "change"]);


$r->addRoute('POST','/register', ['App\Controllers\RegisterController', 'register']);
$r->addRoute('POST','/login', ['App\Controllers\LoginController', 'login']);
$r->addRoute('GET', '/logout', ['App\Controllers\LoginController', 'logout']);


$r->addRoute('GET', "/photos", ["App\controllers\PhotosController", "index"]);
$r->addRoute('GET', '/photos/{id:\d+}', ['App\Controllers\PhotosController', 'show']);
$r->addRoute('GET', '/photos/create', ['App\Controllers\PhotosController', 'create']);
$r->addRoute('POST', '/photos/store', ['App\Controllers\PhotosController', 'store']);
$r->addRoute('GET', '/photos/{id:\d+}/edit', ['App\Controllers\PhotosController', 'edit']);
$r->addRoute('POST', '/photos/{id:\d+}/update', ['App\Controllers\PhotosController', 'update']);
$r->addRoute('GET', '/photos/{id:\d+}/delete', ['App\Controllers\PhotosController', 'delete']);
$r->addRoute('GET', '/photos/{id:\d+}/download', ['App\Controllers\PhotosController', 'download']);


$r->addRoute('GET', "/profile/info", ["App\controllers\ProfileController", "showInfo"]);
$r->addRoute('POST', "/profile/info", ["App\controllers\ProfileController", "updateInfo"]);
$r->addRoute('GET', "/profile/security", ["App\controllers\ProfileController", "showSecurity"]);
$r->addRoute('POST', "/profile/security", ["App\controllers\ProfileController", "updateSecurity"]);



    $r->addGroup('/admin', function (RouteCollector $r) {
        $r->addRoute('GET', '', ["App\controllers\Admin\HomeController", "index"]);

        $r->addRoute('GET', '/categories', ['App\Controllers\Admin\CategoriesController', 'index']);
        $r->addRoute('GET', '/categories/create', ['App\Controllers\Admin\CategoriesController', 'create']);
        $r->addRoute('POST', '/categories/store', ['App\Controllers\Admin\CategoriesController', 'store']);
        $r->addRoute('GET', '/categories/{id:\d+}/edit', ['App\Controllers\Admin\CategoriesController', 'edit']);
        $r->addRoute('POST', '/categories/{id:\d+}/update', ['App\Controllers\Admin\CategoriesController', 'update']);
        $r->addRoute('GET', '/categories/{id:\d+}/delete', ['App\Controllers\Admin\CategoriesController', 'delete']);

        $r->addRoute('GET', '/users', ['App\Controllers\Admin\UsersController', 'index']);
        $r->addRoute('GET', '/users/create', ['App\Controllers\Admin\UsersController', 'create']);
        $r->addRoute('POST', '/users/store', ['App\Controllers\Admin\UsersController', 'store']);
        $r->addRoute('GET', '/users/{id:\d+}/edit', ['App\Controllers\Admin\UsersController', 'edit']);
        $r->addRoute('POST', '/users/{id:\d+}/update', ['App\Controllers\Admin\UsersController', 'update']);
        $r->addRoute('GET', '/users/{id:\d+}/delete', ['App\Controllers\Admin\UsersController', 'delete']);

        $r->addRoute('GET', '/photos', ['App\Controllers\Admin\PhotosController', 'index']);
        $r->addRoute('GET', '/photos/create', ['App\Controllers\Admin\PhotosController', 'create']);
        $r->addRoute('POST', '/photos/store', ['App\Controllers\Admin\PhotosController', 'store']);
        $r->addRoute('GET', '/photos/{id:\d+}/edit', ['App\Controllers\Admin\PhotosController', 'edit']);
        $r->addRoute('POST', '/photos/{id:\d+}/update', ['App\Controllers\Admin\PhotosController', 'update']);
        $r->addRoute('GET', '/photos/{id:\d+}/delete', ['App\Controllers\Admin\PhotosController', 'delete']);
    });
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ...
        dd("404 Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ...
        dd("405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $container->call($handler, $vars);
        // ... call $handler with $vars
        break;
}