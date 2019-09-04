<?php

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\View\ViewServiceProvider;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

define('STARTTIME', microtime(true));
define('BASEDIR', dirname(__DIR__));
define('APP', BASEDIR . '/app');
define('HOME', BASEDIR . '/public');
define('UPLOADS', HOME . '/uploads');
define('RESOURCES', BASEDIR . '/resources');
define('STORAGE', BASEDIR . '/storage');
define('MODULES', BASEDIR . '/modules');
define('SITETIME', time());
define('VERSION', '8.0-dev');

require_once BASEDIR . '/vendor/autoload.php';

if (! env('APP_ENV')) {
    $dotenv = Dotenv::create(BASEDIR);
    $dotenv->load();
}

if (env('APP_DEBUG') && class_exists(Run::class)) {
    $whoops = new Run();

    if (Whoops\Util\Misc::isCommandLine()) {
        $whoops->prependHandler(new PlainTextHandler);
    } else {
        $whoops->prependHandler(new PrettyPageHandler);
    }

    $whoops->prependHandler(static function() {
        $_SERVER = Arr::except($_SERVER, array_keys($_ENV));
        $_ENV    = [];
    });
    $whoops->register();
}

$db = new DB();
$db->addConnection([
    'driver'    => env('DB_DRIVER'),
    'port'      => env('DB_PORT'),
    'host'      => env('DB_HOST'),
    'database'  => env('DB_DATABASE'),
    'username'  => env('DB_USERNAME'),
    'password'  => env('DB_PASSWORD'),
    'charset'   => env('DB_CHARSET'),
    'collation' => env('DB_COLLATION'),
]);

/*$db->setEventDispatcher(new Dispatcher(new Container));*/
$db->setAsGlobal();
$db->bootEloquent();
$db::connection()->enableQueryLog();

/**
 * Setup a new app instance container
 */
$app = new Container();

$app->singleton('files', static function () {
    return new Filesystem();
});

$app->singleton('events', static function () {
    return new Dispatcher();
});

$app->singleton('config', static function () {
    return [
        'view.paths' => [
            HOME . '/themes/' . setting('themes') . '/views',
            RESOURCES . '/views',
            HOME . '/themes',
        ],
        'view.compiled' => STORAGE . '/caches',
    ];
});

$app->singleton('translator', static function () {
    $translator = new Translator(
        new FileLoader(
            new Filesystem(),
            RESOURCES . '/lang'
        ),
        setting('language')
    );

    $translator->setFallback(setting('language_fallback'));

    return $translator;
});

(new ViewServiceProvider($app))->register();

/**
 * Set $app as FacadeApplication handler
 */
Facade::setFacadeApplication($app);
