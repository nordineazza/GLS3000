<?php
namespace Models;

use Silex\Application;

class CoursQueries
{
    private $app;
    const NOM_TABLE= "V_RECHERCHE_COURS";

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /** Récupère les libellés des cours.
     * @param Application $app instance de l'application
     */
    public static function getLibelleCours(Application $app){
        $requete = "SELECT DISTINCT LIBELLE_EC FROM V_RECHERCHE_COURS";
        return $app['db']->fetchAll($requete);
    }

    /**
     * Récupère un cours par un libelle et une année
     * @param Application $app instance de l'application
     * @param $libelle libellé d'un cours
     * @return mixed
     */
    public function getCoursByLibelleAndYear(Application $app, $libelle){
        $coursEscape = str_replace("'", "''", $libelle);

        $requete = "SELECT * FROM V_RECHERCHE_COURS WHERE LIBELLE_EC = '".$coursEscape."'";
        $requete .= " AND ANNEE = ".$app['annee'];
        return $app['db']->fetchAll($requete);
    }

    /**
     * Récupère un V_EC par libellé et par année
     * @param Application $app instance de l'application
     * @param $libelle libellé d'un cours
     * @return mixed
     */
    public function getV_ECByLibelleAndYear(Application $app, $libelle){
        $coursEscape = str_replace("'", "''", $libelle);

        $requete = "SELECT * FROM V_EC WHERE LIBELLE_EC = '" . $coursEscape . "'";
        $requete .= " AND ANNEE = ".$app['annee'];
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static function getListGroupe(Application $app){
        $requete = "SELECT ID_GROUPE, LIB_GROUPE FROM GROUPE ORDER BY LIB_GROUPE";
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static function getListChgFct(Application $app){
        $requete = "SELECT ID_CHG, LIB_CHG FROM CHG_FCT ORDER BY LIB_CHG";
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static function getListEC(Application $app){
        $requete = "SELECT ID_EC, LIBELLE_EC FROM EC ORDER BY LIBELLE_EC";
        return $app['db']->fetchAll($requete);
    }
}

?>
