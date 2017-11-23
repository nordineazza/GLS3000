<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\LigneServiceQueries;
use Models\Querie;

class HomeControllers {

    /**
     * Page d'accueil du site
     * @return un template twig qui correspond à la page d'accueil
     * */
    function indexPage(Application $app) {
        return $app['twig']->render('@views_suivi/base.html.twig', array('cache' => false,
                    'auto_reload' => true
        ));
    }

    function rechercheStatut(Application $app) {
        //$query = new Querie($app);

        $statut = isset($_POST['statut']) ? $_POST['statut'] : false;

        //AUTO-COMPLETION
        $requete = "SELECT DISTINCT LIBELLE_STAT FROM STATUT";
        $tableStatut = $app['db']->fetchAll($requete);

        foreach ($tableStatut as $key => $value)
            $arrayStatut[] = $value['LIBELLE_STAT'];
        $statutJson = json_encode($arrayStatut);

        if ($statut) {//Si la personne a effectué une recherche
            $requete = "SELECT * FROM GLS_R_STATUT_TABLE WHERE STATUT LIKE '%" . $statut . "%'";
            $tableStatut = $app['db']->fetchAll($requete);
            $recherche = true;
        }

        //$tableStatut= $query ->getRStatut($app);
        return $app['twig']->render('@views_suivi/recherche.statut.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'statut' => $tableStatut,
                    'recherche' => $recherche,
                    'statutJson' => $statutJson
        ));
    }

    function rechercheCours(Application $app, $recherche = false) {
        $cours = isset($_POST['cours']) ? $_POST['cours'] : false;

        $get = $app['request']->query->all();
        //AUTO-COMPLETION
        $requete = "SELECT DISTINCT LIBELLE_EC FROM V_RECHERCHE_COURS";
        $tableCours = $app['db']->fetchAll($requete);

        foreach ($tableCours as $key => $value)
            $arrayCours[] = $value['LIBELLE_EC'];
        
        $coursJson = json_encode($arrayCours);
        $coursEscape = (!empty($get)) ? str_replace("'", "''", $get['cours']) : str_replace("'", "''", $cours);
        $annee = $app['annee'];
        if ($cours || !empty($get)) {//Si la personne a effectué une recherche
            $requete = "SELECT * FROM V_RECHERCHE_COURS WHERE LIBELLE_EC = '" . $coursEscape . "' AND ANNEE = '$annee'";
            $tableCours = $app['db']->fetchAll($requete);
            $requete2 = "SELECT * FROM V_LIGNE_SERVICE_EC WHERE LIBELLE_EC = '" . $coursEscape . "' AND ANNEE = '$annee'";
            $tableLsCours = $app['db']->fetchAll($requete2);

            $requete3 = "SELECT * FROM V_EC WHERE LIBELLE_EC = '" . $coursEscape . "' AND ANNEE = '$annee' ";
            $Desc_EC = $app['db']->fetchAll($requete3);

            $recherche = true;
        }

        return $app['twig']->render('@views_suivi/recherche.cours.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'cours' => $tableCours,
                    'lignes' => $tableLsCours,
                    'desc_EC' => $Desc_EC[0],
                    'recherche' => $recherche,
                    'coursName' => $coursEscape, 
                    'annee' => $app['annee'],
                    'coursJson' => $coursJson
        ));
    }

    function rechercheEnseignant(Application $app, $recherche = false) {
        $get = $app['request']->query->all();
        if (!empty($get)) {
            $enseignant = $get['nomEns'];
        } else {
            $enseignant = isset($_POST['enseignant']) ? $_POST['enseignant'] : false;
        }

        $requete3 = "SELECT DISTINCT NOM FROM PERSONNEL";
        $tableEns = $app['db']->fetchAll($requete3);
        foreach ($tableEns as $key => $value)
            $arrayEns[] = $value["NOM"];
        $ensJson = json_encode($arrayEns);

        if ($enseignant) {
            $annee = $app['annee'];
            $requete = "SELECT * FROM V_R_ENSEIGNANT_CHG_FCT WHERE NOM = '$enseignant' AND ANNEE = '$annee' ";
            $tableEns = $app['db']->fetchAll($requete);
            $requete2 = "SELECT * FROM V_R_ENSEIGNANT_COURS WHERE NOM like '%" . $enseignant . "%' AND ANNEE = '$annee'";
            $tableEC = $app['db']->fetchAll($requete2);
            $requete4 = "select NOM, PRENOM, LIBELLE_STAT, NB_H_MIN, NB_H_MAX from personnel, statut where NOM like '%" . $enseignant . "%' AND personnel.fid_stat = statut.id_stat";
            $tableInfEns = $app['db']->fetchAll($requete4);
            $nom = $tableInfEns[0]['NOM'];
            $prenom = $tableInfEns[0]['PRENOM'];
            $statut = $tableInfEns[0]['LIBELLE_STAT'];
            $nbhmin = $tableInfEns[0]['NB_H_MIN'];
            $nbhmax = $tableInfEns[0]['NB_H_MAX'];
            $app['titreImpression'] = "Service de $prenom $nom, $statut";
            $recherche = true;
        }

        return $app['twig']->render('@views_suivi/recherche.enseignant.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'load_chg' => $tableEns,
                    'load_ec' => $tableEC,
                    'prof' => $recherche,
                    'ensJson' => $ensJson,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'statut' => $statut,
                    'nbhmin' => $nbhmin,
                    'nbhmax' => $nbhmax
        ));
    }

    function modif(Application $app) {
        $annee = $app['annee'];
        $query = "SELECT * FROM V_LIGNE_SERVICE_CHG" . " WHERE ANNEE = '$annee' ";
        $tableChg = $app['db']->fetchAll($query);
        $query2 = "SELECT * FROM V_LIGNE_SERVICE_EC" . " WHERE ANNEE = '$annee' ";
        $tableEC = $app['db']->fetchAll($query2);

        $requeteGroupe = "SELECT ID_GROUPE, LIB_GROUPE FROM GROUPE ORDER BY LIB_GROUPE";
        $requetePersonnel = "SELECT ID_PERS, NOM, PRENOM FROM PERSONNEL ORDER BY NOM";
        $requeteCharge = "SELECT ID_CHG, LIB_CHG FROM CHG_FCT ORDER BY LIB_CHG";
        $requeteMatiere = "SELECT ID_EC, LIBELLE_EC FROM EC ORDER BY LIBELLE_EC";

        $allGroupe = $app['db']->fetchAll($requeteGroupe);
        $allPersonnel = $app['db']->fetchAll($requetePersonnel);
        $allCharge = $app['db']->fetchAll($requeteCharge);
        $allMatiere = $app['db']->fetchAll($requeteMatiere);

        return $app['twig']->render('@views_suivi/modif.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'chg' => $tableChg,
                    'ec' => $tableEC,
                    'groupe' => $allGroupe,
                    'personnel' => $allPersonnel,
                    'charge' => $allCharge,
                    'matiere' => $allMatiere
        ));
    }

    function modifAjaxEC(Application $app) {

        $id = array("ID_LIGNE" => $_POST['id_ligne']);
        $aModif = $_POST;
        foreach ($aModif as $key => $value) {
            if ($value == -1) {
                unset($aModif[$key]);
            }
        }
        unset($aModif['id_ligne']);
        $app['db']->update("LIGNE_SERVICE", $aModif, $id);
        return 1;
    }

    function modifAjaxChg(Application $app) {

        $id = array("ID_LIGNE" => $_POST['id_ligne']);
        $aModif = $_POST;
        foreach ($aModif as $key => $value) {
            if ($value == -1) {
                unset($aModif[$key]);
            }
        }
        unset($aModif['id_ligne']);
        $app['db']->update("LIGNE_SERVICE", $aModif, $id);
        return 1;
    }

    function ajoutLigne(Application $app) {
        $var = $_POST;
        foreach ($var as $key => $value) {
            if (empty($value)) {
                unset($var[$key]);
            }
        }

        //Si on ajoute un EC
        if ($_POST['type'] == 1) {
            unset($var["FID_CHGFCT"]);
        }
        //Si on ajoute une charge de fonction
        if ($_POST['type'] == 2) {
            unset($var["FID_EC"]);
            unset($var["H_CM"]);
            unset($var["H_EAD"]);
            unset($var["H_TP"]);
        }
        unset($var["type"]);

        //On recupere le prochain ID pour inserer une ligne de service
        $query = "SELECT MAX(ID_LIGNE) AS MAX FROM LIGNE_SERVICE ORDER BY ID_LIGNE";
        $arrayMax = $app['db']->fetchAll($query);
        $nextID = $arrayMax[0]["MAX"];
        $nextID ++;
        $var["ID_LIGNE"] = $nextID;

        return $app['db']->insert("LIGNE_SERVICE", $var);
    }

    function suppLigne(Application $app) {
        $req = "select * from LIGNE_SERVICE WHERE ID_LIGNE='" . $_POST['ID_LIGNE'] . "'";
        $ls = $app['db']->fetchAll($req);
        $FID_GROUPE = $ls[0]['FID_GROUPE'];
        $FID_EC = $ls[0]['FID_EC'];
        $FID_PERS = $ls[0]['FID_PERS'];
        $SEM = $ls[0]['SEM'];
        $ANNEE = $ls[0]['ANNEE'];


        if (!empty($FID_EC) && ($FID_PERS != "404")) {
            $req = "Select COUNT(*) as NB 
				FROM ligne_service
				WHERE fid_pers = 404
				AND fid_groupe = '" . $FID_GROUPE . "'
				AND fid_ec = '" . $FID_EC . "'
				AND sem='" . $SEM . "'
				AND annee='" . $ANNEE . "'";
            $countTable = $app['db']->fetchAll($req);
            $count = $countTable[0]['NB'];

            if ($count != 0) {
                $req = "Select * 
				FROM ligne_service
				WHERE fid_pers = 404
				AND fid_groupe = '" . $FID_GROUPE . "'
				AND fid_ec = '" . $FID_EC . "'
				AND sem='" . $SEM . "'
				AND annee='" . $ANNEE . "'";
                $oldLigne = $app['db']->fetchAll($req);
                $id = array('ID_LIGNE' => $oldLigne[0]['ID_LIGNE']);

                $ls[0]['FID_PERS'] = 404;
                $ls[0]['H_CM'] = $ls[0]['H_CM'] + $oldLigne[0]['H_CM'];
                $ls[0]['H_TD'] = $ls[0]['H_TD'] + $oldLigne[0]['H_TD'];
                $ls[0]['H_EAD'] = $ls[0]['H_EAD'] + $oldLigne[0]['H_EAD'];
                $ls[0]['H_TP'] = $ls[0]['H_TP'] + $oldLigne[0]['H_TP'];
                $var = $ls[0];
                unset($var["ID_LIGNE"]);


                $app['db']->update("LIGNE_SERVICE", $var, $id);
            } else {
                $ls[0]['FID_PERS'] = 404;
                $query = "SELECT MAX(ID_LIGNE) AS MAX FROM LIGNE_SERVICE ORDER BY ID_LIGNE";
                $arrayMax = $app['db']->fetchAll($query);
                $nextID = $arrayMax[0]["MAX"];
                $nextID ++;
                $ls[0]['ID_LIGNE'] = $nextID;
                $var = $ls[0];
                $app['db']->insert("LIGNE_SERVICE", $var);
            }
        }

        return $app['db']->delete("LIGNE_SERVICE", $_POST);
    }

}

?>
