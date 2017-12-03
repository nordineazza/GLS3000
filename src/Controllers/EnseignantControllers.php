<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\EnseignantQueries;
use Helpers\HelperMethods;

class EnseignantControllers
{
    /**
     * Page de recherche d'un enseignant.
     * @param Application $app
     * @return mixed
     */
    function rechercheEnseignant(Request $request, Application $app)
    {
        $get = $request->query->all(); //Correspond au $_GET
        $enseignantQueries = new EnseignantQueries($app);
        $tableEns = $enseignantQueries->getListNomPersonnelEnseignant($app);
        $ensJson = HelperMethods::convertArrayToJson($tableEns, "NOM");

        if (!empty($get)) $enseignant = $get['enseignant'];
        if ($enseignant) {
            // Redirect to /Enseignant/Nom_Enseignant
            return $app->redirect("Enseignant/$enseignant");
        }
        return $app['twig']->render('@views/enseignant.html.twig', array('cache' => false,
            'auto_reload' => true,
            'rechercheActive' => true,
            'ensJson' => $ensJson));
    }
    /**
     * @param Application $app
     * @return mixed
     */
    function getEnseignant(Request $request, Application $app, $enseignant)
    {
        $get = $request->query->all();
        $enseignantQueries = new EnseignantQueries($app);
        if (!empty($get)) $enseignant = $get['enseignant'];

        if ($enseignant) {
            $tableEns = $enseignantQueries->getLigneEnseignantByNom($app, $enseignant);
            $tableEC = $enseignantQueries->getLigneEnseignantCours($app, $enseignant);
            $tableInfoEns = $enseignantQueries->getInformationEnseignantByNomEnseignant($app, $enseignant);
            if(empty($tableInfoEns)){
                $app->abort(404, "Le professeur $enseignant n'existe pas.");
            }
        }
        return $app['twig']->render('@views/enseignant.html.twig', array('cache' => false,
            'auto_reload' => true,
            'load_chg' => $tableEns,
            'load_ec' => $tableEC,
            'rechercheActive' => false,
            'nom' => $tableInfoEns[0]['NOM'],
            'prenom' => $tableInfoEns[0]['PRENOM'],
            'statut' => $tableInfoEns[0]['LIBELLE_STAT'],
            'nbhmin' => $tableInfoEns[0]['NB_H_MIN'],
            'nbhmax' => $tableInfoEns[0]['NB_H_MAX']
        ));
    }
}
