<?php
namespace Models;

use Silex\Application;


class LigneServiceQueries
{
	private $app;
	const NOM_TABLE= "LIGNE_SERVICE";

	public function __construct(Application $app)
	{
		$this->app = $app;
	}
    /**
     * Récupère une Ligne de Service selon un identifiant passé en paramètre.
     * @param Application $app instance de l'application.
     * @param id $id identifiant d'une ligne de service
     * @return mixed
     */
    public static function getLigneServiceById(Application $app, $id){
        $requete = "select * from LIGNE_SERVICE WHERE ID_LIGNE='".$id."'";
        return $app['db']->fetchAll($requete);
    }
    /**
     * Récupère une ligne de service par libellé et par année.
     * @param Application $app instance de l'application
     * @param $libelle libellé d'un cours
     * @return mixed
     */
    public function getLigneServiceByLibelleAndYear(Application $app, $libelle){
        $coursEscape = str_replace("'", "''", $libelle);

        $requete = "SELECT * FROM V_LIGNE_SERVICE_EC WHERE LIBELLE_EC = '".$coursEscape."'";
        $requete .= " AND ANNEE = ".$app['annee'];
        return $app['db']->fetchAll($requete);
    }
    /**
     * Ligne de service (Charge de fonction) selon l'année en cours.
     * @param Application $app instance de l'application.
     * @return mixed
     */
    public static function getLigneServiceCHGByYear(Application $app){
        $query = "SELECT * FROM V_LIGNE_SERVICE_CHG" . " WHERE ANNEE =".$app['annee'];
        return $app['db']->fetchAll($query);
    }
    /**
     * Ligne de service (EC) selon l'année en cours.
     * @param Application $app instance de l'application.
     * @return mixed
     */
    public static function getLigneServiceECByYear(Application $app){
        $query2 = "SELECT * FROM V_LIGNE_SERVICE_EC" . " WHERE ANNEE =".$app['annee'];
        return $app['db']->fetchAll($query2);
    }
    /**
     * Récupère une liste de Ligne de service selon des paramètres passés.
     * @param Application $app instance de l'application.
     * @param $tableLs
     * @return mixed
     */
    public static function getLigneServiceByParameters(Application $app, $tableLs){
        $fidGroupe = $tableLs[0]['FID_GROUPE'];
        $fidEC = $tableLs[0]['FID_EC'];
        $sem = $tableLs[0]['SEM'];
        $annee = $tableLs[0]['ANNEE'];
        $requete = "SELECT * FROM ligne_service WHERE fid_pers = 404";
        $requete .= " AND fid_groupe = '".$fidGroupe."' AND fid_ec = '".$fidEC."'";
        $requete .= " AND sem='".$sem."' AND annee='".$annee."'";
        echo $requete;
        return $app['db']->fetchAll($requete);

    }
    /**
     * Insérer une ligne de service en base de données.
     * @param Application $app instance de l'application
     * @param $ligneService array d'une ligne de service à insérer.
     * @return mixed
     */
    public static function insertLigneService(Application $app, $ligneService){
        return $app['db']->insert("LIGNE_SERVICE", $ligneService);
    }
    /**
     * Récupère le dernier ID utilisé dans la table LIGNE_SERVICE.
     * @param Application $app instance de l'application
     * @return le dernier ID renseigné en base de données pour la table LIGNE_SERVICE.
     */
    public static function getMaxIdFromLigneService(Application $app){
        $query = "SELECT MAX(ID_LIGNE) AS MAX FROM LIGNE_SERVICE ORDER BY ID_LIGNE";
        $array = $app['db']->fetchAll($query);
        return $array[0]["MAX"];
    }
    /**
     * Update d'une ligne de service en base de données.
     * @param Application $app instance de l'application
     * @param $ligneService Ligne de service à mettre à jour.
     * @param $where la condition qui indique la/les ligne(s) à mettre à jour.
     * @return mixed
     */
    public static function updateLigneService(Application $app, $ligneService, $where){
        return $app['db']->update("LIGNE_SERVICE", $ligneService, $where);
    }
    /**
     * Delete d'une ligne de service en base de données.
     * @param Application $app instance de l'application
     * @param $ligneService Ligne de service à supprimer.
     * @return mixed
     */
    public static function deleteLigneService(Application $app, $ligneService){
        return $app['db']->delete("LIGNE_SERVICE", $ligneService);
    }

}

?>
