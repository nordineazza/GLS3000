<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\StatutQueries;
use Helpers\HelperMethods;

class StatutControllers {

    /**
     * Page de recherche d'un statut.
     * @param Application $app
     * @return mixed
     */
    function rechercheStatut(Application $app, Request $request)
    {
        $get = $request->query->all(); //Correspond au $_GET
        $statutQueries = new StatutQueries($app);
        $tableStatut = $statutQueries -> getListLibelleStatut($app); //Pour l'auto-complétion
        $statutJson = HelperMethods::convertArrayToJson($tableStatut, "LIBELLE_STAT");
        if (!empty($get)) $statut = $get['statut'];

        if ($statut) {
            // Redirect to /Staut/Nom_Statut
            return $app->redirect("Statut/$statut");
        }
        return $app['twig']->render('@views/statut.html.twig', array('cache' => false,
            'auto_reload' => true,
            'recherche' => true,
            'statutJson' => $statutJson
        ));
    }
    /**
     * @param Application $app
     * @return mixed
     */
    function getStatut(Application $app, Request $request, $statut) {
        $get = $request->query->all();
        $statutQueries = new StatutQueries($app);
        if (!empty($get)) $statut = $get['statut'];

        if ($statut) { // Si la personne a effectué une recherche
            $tableStatut = $statutQueries -> getGlsRstatutTable($app, $statut);
            if(empty($tableStatut)){
                $app->abort(404, "Le statut $statut n'existe pas.");
            }

        }
        return $app['twig']->render('@views/statut.html.twig', array('cache' => false,
            'auto_reload' => true,
            'statut' => $tableStatut,
            'recherche' => false
        ));
    }

}
?>