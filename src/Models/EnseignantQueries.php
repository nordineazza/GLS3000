<?php

namespace Models;

use Silex\Application;

class EnseignantQueries
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Liste des noms du personnel enseignant
     * @param Application $app instance de l'application
     * @return mixed
     */
    public static function getListNomPersonnelEnseignant(Application $app)
    {
        $requete = "SELECT DISTINCT NOM FROM PERSONNEL";
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @param $enseignant
     * @return mixed
     */
    public static function getLigneEnseignantByNom(Application $app, $enseignant)
    {
        $requete = "SELECT * FROM V_R_ENSEIGNANT_CHG_FCT WHERE NOM = '$enseignant'";
        $requete .= " AND ANNEE =".$app['annee'];
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @param $enseignant
     * @return mixed
     */
    public static  function getLigneEnseignantCours(Application $app, $enseignant){
        $requete = "SELECT * FROM V_R_ENSEIGNANT_COURS WHERE NOM like '%" . $enseignant . "%'";
        $requete .= " AND ANNEE =".$app['annee'];
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @param $enseignant
     * @return mixed
     */
    public static function getInformationEnseignantByNomEnseignant(Application $app, $enseignant){
        $requete = "SELECT NOM, PRENOM, LIBELLE_STAT, NB_H_MIN, NB_H_MAX FROM personnel, statut ";
        $requete .= "WHERE NOM like '%".$enseignant."%' AND personnel.fid_stat = statut.id_stat";
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static  function getListPersonnel(Application $app){
        $requete = "SELECT ID_PERS, NOM, PRENOM FROM PERSONNEL ORDER BY NOM";
        return $app['db']->fetchAll($requete);
    }
    
}

?>