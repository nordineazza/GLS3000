<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\LigneServiceQueries;
use Models\CoursQueries;
use Models\EnseignantQueries;
use Helpers\HelperMethods;

class LigneServiceControllers
{
    /**
     * Page de ligne de services.
     * @param Application $app instance de l'application.
     * @return mixed
     */
    function pageModification(Application $app) {
        $ligneServiceQueries = new LigneServiceQueries($app);
        $enseignantQueries = new EnseignantQueries($app);
        $coursQueries = new CoursQueries($app);

        $tableChg = $ligneServiceQueries -> getLigneServiceCHGByYear($app);
        $tableEC = $ligneServiceQueries -> getLigneServiceECByYear($app);
        $allGroupe = $coursQueries -> getListGroupe($app);
        $allPersonnel = $enseignantQueries -> getListPersonnel($app);
        $allCharge = $coursQueries -> getListChgFct($app);;
        $allMatiere = $coursQueries -> getListEC($app);

        return $app['twig']->render('@views/ligne.service.html.twig', array('cache' => false,
            'auto_reload' => true,
            'chg' => $tableChg,
            'ec' => $tableEC,
            'groupe' => $allGroupe,
            'personnel' => $allPersonnel,
            'charge' => $allCharge,
            'matiere' => $allMatiere
        ));
    }
    /**
     * Ajout d'une ligne de service de type EC ou Charge de fonction
     * @param Application $app instance de l'application
     * @param Request $request contient les éléments $_GET et $_POST entre autres.
     * @return mixed
     */
    function ajoutLigne(Application $app, Request $request) {
        $ligneServiceQueries = new LigneServiceQueries($app);
        $post = $request->request->all();
        $ligneService = HelperMethods::removeEmptyValueFromArray($post);
        // On traite deux cas : l'ajout d'un EC ou l'ajout d'une charge de fonction
        // 1) Ajout d'un EC
        if ($ligneService['type'] == 1) {
            unset($ligneService["FID_CHGFCT"]);
        }
        else{
            // 2) Ajout d'une charge de fonction
            unset($ligneService["FID_EC"]);
            unset($ligneService["H_CM"]);
            unset($ligneService["H_EAD"]);
            unset($ligneService["H_TP"]);
        }
        unset($ligneService["type"]); // On retire l'élément type qui nous servait à distinguer un EC d'une Charge de fonction
        //On recupere le dernière ID de la table LIGNE_SERVICE
        $lastID = $ligneServiceQueries->getMaxIdFromLigneService($app);
        $nextID = $lastID + 1;
        $ligneService["ID_LIGNE"] = $nextID;

        return $ligneServiceQueries->insertLigneService($app, $ligneService);
    }
    /**
     * Met à jour les lignes de services EC et/ou Charge de fonction
     * @param Application $app instance de l'application
     * @param Request $request contient les éléments $_GET et $_POST entre autres.
     * @return mixed
     */
    function updateLigneService(Application $app, Request $request) {
        $ligneServiceQueries = new LigneServiceQueries($app);
        $post = $request->request->all();
        $id = array("ID_LIGNE" => $post['id_ligne']);
        $newLigneService = HelperMethods::removeEmptyValueFromArray($post);
        unset($newLigneService['id_ligne']);
        return $ligneServiceQueries->updateLigneService($app, $newLigneService, $id);
    }
    /**
     * Supprime les lignes de services EC et/ou Charge de fonction
     * @param Application $app instance de l'application
     * @param Request $request contient les éléments $_GET et $_POST entre autres.
     * @return mixed
     */
    function suppLigne(Application $app, Request $request) {
        $ligneServiceQueries = new LigneServiceQueries($app);
        $post = $request->request->all();
        $id = $post['ID_LIGNE'];
        $ls = $ligneServiceQueries -> getLigneServiceById($app, $id);
        $FID_EC = $ls[0]['FID_EC'];
        $FID_PERS = $ls[0]['FID_PERS'];
        if (!empty($FID_EC) && ($FID_PERS != "404")) {
            $oldLigne = $ligneServiceQueries->getLigneServiceByParameters($app, $ls);
            if (!empty($oldLigne)) {
                $id = array('ID_LIGNE' => $oldLigne[0]['ID_LIGNE']);
                $ls[0]['FID_PERS'] = 404;
                $ls[0]['H_CM'] = $ls[0]['H_CM'] + $oldLigne[0]['H_CM'];
                $ls[0]['H_TD'] = $ls[0]['H_TD'] + $oldLigne[0]['H_TD'];
                $ls[0]['H_EAD'] = $ls[0]['H_EAD'] + $oldLigne[0]['H_EAD'];
                $ls[0]['H_TP'] = $ls[0]['H_TP'] + $oldLigne[0]['H_TP'];
                $var = $ls[0];
                unset($var["ID_LIGNE"]);
                $ligneServiceQueries->updateLigneService($app, $var, $id);
            } else {
                $ls[0]['FID_PERS'] = 404;
                //On recupere le dernière ID de la table LIGNE_SERVICE
                $lastID = $ligneServiceQueries->getMaxIdFromLigneService($app);
                $nextID = $lastID + 1;
                $ls[0]['ID_LIGNE'] = $nextID;
                $var = $ls[0];
                $ligneServiceQueries->insertLigneService($app, $var);
            }
        }
        return $ligneServiceQueries->deleteLigneService($app, $post);
    }
}
?>