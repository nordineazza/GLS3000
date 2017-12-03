<?php
ini_set('memory_limit', '-1');

// loader le framework
$loader = require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/fpdf/fpdf181/fpdf.php';
define('FPDF_FONTPATH',__DIR__ .'/../vendor/fpdf/fpdf181/font');

// instantier $app comme objet de Silex framework
$app = new Silex\Application();
// init les params dir de bases
$baseDir          = dirname(dirname(__FILE__));
$aBaseDir         = explode(DIRECTORY_SEPARATOR, $baseDir);
$rootFolder       = end($aBaseDir);
$app['paths']     = ['rootFolder' => $rootFolder, 'baseDir' => $baseDir, 'webDir' => dirname(__FILE__), 
    'vendorDir' => $baseDir . '/vendor', 'appDir' => $baseDir . '/app', 'srcDir' => $baseDir . '/src'];

$app['rootRoute'] = '/'.$rootFolder;
// Register the Security Service Provider
$app->register(new Silex\Provider\SessionServiceProvider());
// Twig configuration
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => $app['paths']['srcDir']."/Views"
));

// charger le fichier de configuration
$ini_config = parse_ini_file($app['paths']['appDir'].'/config.ini', TRUE);
// récupérer des info de connexion BD
$config = $ini_config['developpement'];

//catch all errors and convert them to exceptions
Symfony\Component\Debug\ExceptionHandler::register($app['debug']);

// array info data base
$app['db.options'] = array(
    'driver'    => $config['db.driver'],
    'dbname'    => $config['db.dbname'],
    'host'      => $config['db.host'],
    'port'      => $config['db.port'],
    'user'      => $config['db.user'],
    'password'  => $config['db.password'],
    'charset'   => $config['db.charset'],
);

// instancier Doctrine DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options'],
));

// Récupérer tous les routes avant de lancer l'application
require_once $app['paths']['srcDir'].'/Routes.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// 404 - Page not found
$app->error(function (\Exception $e, $code) use ($app) {
    echo $e;
    switch ($code) {
        case 404:
            $message = 'We are sorry, but something went terribly wrong.';
            break;
        default:
            $message = 'The requested page could not be found.';
            return $app['twig']->render('@views_suivi/404.html.twig', array('cache' => false,
                'auto_reload' => true));
    }
    return new Response($message);
});

$app['debug'] = true;
$app['annee'] = 2017;
$app['titreImpression'] = "";

//Enregistrement du service de logs
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.name' => $rootFolder,
    'monolog.logfile' => $app['paths']['appDir']. '/logs/app/'.date('Y-m-d').'.log',
    'monolog.level' => $app['debug'] ? Monolog\Logger::DEBUG : Monolog\Logger::INFO,
));

$app->run(); 

?>	
