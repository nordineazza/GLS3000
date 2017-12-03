<?php

//Définition du chemin des vues et des routes

$app['twig.loader.filesystem']->addPath($app['paths']['srcDir']."/Views/","views");
//Définir un préfixe de route pour ce module
$gls3000 = $app['controllers_factory'];
$app->mount("/GLS3000/",$gls3000);

//Définition des routes subjacente + accronymes de module
$gls3000->get('/','Controllers\HomeControllers::indexPage')->bind('suiviIndex'); //Route vers la page d'accueil.
$gls3000->get('/Statut','Controllers\StatutControllers::rechercheStatut')->bind('rechercheStatut');
$gls3000->get('/Statut/{statut}','Controllers\StatutControllers::getStatut')->bind('getStatut');

$gls3000->get('/Cours','Controllers\CoursControllers::rechercheCours')->bind('rechercheCours');
$gls3000->get('/Cours/{cours}','Controllers\CoursControllers::getCours')->bind('getCours')->assert('cours', '.+');;
$gls3000->get('/Enseignant','Controllers\EnseignantControllers::rechercheEnseignant')->bind('rechercheEnseignant');
$gls3000->get('/Enseignant/{enseignant}','Controllers\EnseignantControllers::getEnseignant')->bind('getEnseignant');

$gls3000->get('/LigneService','Controllers\LigneServiceControllers::pageModification')->bind('modif');
$gls3000->post('/LigneService/Ajouter','Controllers\LigneServiceControllers::ajoutLigne')->bind('ajoutLigne');
$gls3000->post('/LigneService/Modifier/EC','Controllers\LigneServiceControllers::updateLigneService')->bind('modifAjaxEC');
$gls3000->post('/LigneService/Modifier/Chg','Controllers\LigneServiceControllers::updateLigneService')->bind('modifAjaxChg');
$gls3000->post('/LigneService/Supprimer','Controllers\LigneServiceControllers::suppLigne')->bind('suppLigne');

?>