<?php

### Module de Suivi des affaires ###
//Définition du chemin des views et des accronymes pour ce module

$app['twig.loader.filesystem']->addPath($app['paths']['srcDir']."/Views/","views_suivi");
//Définir un préfixe de route pour ce module
$gls3000 = $app['controllers_factory'];
$app->mount("/",$gls3000);

//Définition des routes subjacente + accronymes de module
$gls3000->get('/','Controllers\HomeControllers::indexPage')->bind('suiviIndex'); //Route vers la page d'accueil.

$gls3000->match('/statut','Controllers\HomeControllers::rechercheStatut')->method('POST|GET')->bind('rechercheStatut'); //Route vers la page d'affichage par statut.

$gls3000->match('/cours','Controllers\HomeControllers::rechercheCours')->method('GET|POST')->bind('rechercheCours'); //Route vers la page d'affichage par cours.

$gls3000->match('/enseignant','Controllers\HomeControllers::rechercheEnseignant')->method('GET|POST')->bind('rechercheEnseignant'); //Route vers la page d'affichage par cours.

$gls3000->get('/Modifier','Controllers\HomeControllers::modif')->bind('modif'); //Route vers la page de modifications des lignes de services.

$gls3000->match('/Modifier/EC','Controllers\HomeControllers::modifAjaxEC')->method('POST|GET')->bind('modifAjaxEC'); //Route vers la page de modifications des lignes de services.

$gls3000->match('/Modifier/Chg','Controllers\HomeControllers::modifAjaxChg')->method('POST|GET')->bind('modifAjaxChg'); //Route vers la page de modifications des lignes de services.

$gls3000->match('/Ajout','Controllers\HomeControllers::ajoutLigne')->method('GET|POST')->bind('ajoutLigne'); //Route vers la fonction d'ajout de ligne.

$gls3000->match('/Supprimer','Controllers\HomeControllers::suppLigne')->method('GET|POST')->bind('suppLigne'); //Route vers la fonction de suppresion d'une ligne.

?>