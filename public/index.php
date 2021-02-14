<?php
/**
 * @author Antonio García García
 * 01/02/2021
 */

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

require_once('../vendor/autoload.php');

use App\Models\Blog;
use Aura\Router\RouterContainer;
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_NAME'],
    'username'  => $_ENV['DB_USER'],
    'password'  => $_ENV['DB_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('index', '/ejercicios/bbdd/symblog/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction'
]);
$map->get('addBlog', '/ejercicios/bbdd/symblog/blogs/add', [
    'controller' => 'App\Controllers\BlogsController',
    'action' => 'getAddBlogAction',
    'auth' => true
]);
$map->post('saveBlog', '/ejercicios/bbdd/symblog/blogs/add', [
    'controller' => 'App\Controllers\BlogsController',
    'action' => 'getAddBlogAction'
]);
$map->get('show', '/ejercicios/bbdd/symblog/blogs/show', [
    'controller' => 'App\Controllers\ShowController',
    'action' => 'showAction'
]);
$map->post('postComment', '/ejercicios/bbdd/symblog/blogs/show', [
    'controller' => 'App\Controllers\ShowController',
    'action' => 'postComment'
]);
$map->get('addUser', '/ejercicios/bbdd/symblog/users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUserAction',
    'auth' => true
]);
$map->post('saveUser', '/ejercicios/bbdd/symblog/users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUserAction'
]);
$map->get('getLogin', '/ejercicios/bbdd/symblog/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);
$map->post('login', '/ejercicios/bbdd/symblog/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);
$map->get('logout', '/ejercicios/bbdd/symblog/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);
$map->get('adminView', '/ejercicios/bbdd/symblog/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);
$handlerData = $route->handler;
$needsAuth = $handlerData['auth'] ?? false;
$sessionUserId = $_SESSION['userId'] ?? null;
if ($needsAuth && !$sessionUserId) {
    header('Location: /ejercicios/bbdd/symblog/login');
} else {
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];

    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }

    http_response_code($response->getStatusCode());
    echo $response->getBody();
}
?>