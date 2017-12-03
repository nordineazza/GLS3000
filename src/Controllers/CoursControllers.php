<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\LigneServiceQueries;
use Models\CoursQueries;
use Helpers\HelperMethods;

class CoursControllers
{
    /**
     * Page de recherche d'un cours.
     * @param Request $request
     * @param Application $app instance du framework
     * @return mixed
     */
    public function rechercheCours(Request $request, Application $app){
        $get = $request->query->all();
        $coursQueries = new CoursQueries($app);
        $tableCours = $coursQueries -> getLibelleCours($app); //Auto-completion
        $coursJson = HelperMethods::convertArrayToJson($tableCours, 'LIBELLE_EC');

        if (!empty($get)) $cours = $get['cours'];
        if ($cours) {
            // Redirect to /Enseignant/Nom_Enseignant
            return $app->redirect("Cours/$cours");
        }
        return $app['twig']->render('@views/cours.html.twig', array('cache' => false,
            'auto_reload' => true,
            'recherche' => true,
            'coursJson' => $coursJson
        ));
    }
    /**
     * Recherche un cours
     * @param Application $app instance de l'application
     * @param bool $recherche
     * @return mixed
     */
    public function getCours(Request $request, Application $app, $cours)
    {
        $get = $request->query->all();
        $ligneServiceQueries = new LigneServiceQueries($app);
        $coursQueries = new CoursQueries($app);
        if(!empty($get)) $cours = $get['cours'];

        if ($cours || !empty($get)) { //si la personne a effectué une recherche
            $tableCours = $coursQueries -> getCoursByLibelleAndYear($app, $cours);
            $tableLsCours = $ligneServiceQueries -> getLigneServiceByLibelleAndYear($app, $cours);
            $descEC = $coursQueries -> getV_ECByLibelleAndYear($app, $cours);
            if(empty($tableCours)){
                $app->abort(404, "Le cours $cours n'existe pas.");
            }
        }
        return $app['twig']->render('@views/cours.html.twig', array('cache' => false,
            'auto_reload' => true,
            'cours' => $tableCours,
            'lignes' => $tableLsCours,
            'desc_EC' => $descEC[0],
            'recherche' => false,
            'coursName' => $cours,
            'annee' => $app['annee']
        ));
    }
}

?>