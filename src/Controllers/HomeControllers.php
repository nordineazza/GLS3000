<?php

namespace Controllers;

use Silex\Application;

class HomeControllers {
    /**
     * Page d'accueil du site
     * @return un template twig qui correspond à la page d'accueil
     * */
    function indexPage(Application $app) {
        return $app['twig']->render('@views/base.html.twig', array('cache' => false,
                    'auto_reload' => true
        ));
    }
}

?>
