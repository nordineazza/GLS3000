<?php
namespace Models;

use Silex\Application;


class Querie
{
	private $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	static public function getRStatut(Application $app)
	{
		$requete = "SELECT * FROM R_STATUT_TABLE";
		return $app['db']->fetchAll($requete);
	}

	static public function getREnseignantChg(Application $app)
	{
		$requete = "SELECT * FROM V_R_ENSEIGNANT_CHG_FCT";
		return $app['db']->fetchAll($requete);
	}

	static public function getRCours(Application $app)
	{
		$requete = "SELECT * FROM V_R_ENSEIGNANT_COURS";
		return $app['db']->fetchAll($requete);
	}
	

}

?>