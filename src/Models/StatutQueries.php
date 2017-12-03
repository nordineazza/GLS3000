<?php

namespace Models;

use Silex\Application;

class StatutQueries
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    /**
     * @param Application $app
     * @return mixed
     */
    public static function getListLibelleStatut(Application $app){
        $requete = "SELECT DISTINCT LIBELLE_STAT FROM STATUT";
        return $app['db']->fetchAll($requete);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static function getGlsRstatutTable(Application $app, $statut){
        $requete = "SELECT * FROM GLS_R_STATUT_TABLE WHERE STATUT = '".$statut."'";
        return $app['db']->fetchAll($requete);
    }
}

?>