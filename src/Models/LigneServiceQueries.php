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

}

?>
