<?php

// Connexion à la BDD
$host_db = 'mysql:host=localhost;dbname=amwa8019_switch'; 
$login = 'amwa8019_wass'; 
$password = 'Wassila87$'; 
$options = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, 
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' 
				);				
$pdo = new PDO($host_db, $login, $password, $options);

// Création d'une variable destinée à afficher des messages utilisateur
$msg = "";

// ouverture d'une session
session_start();

// déclaration de constante
// URL racine du projet
define('URL', 'http://switch.amwa8019.odns.fr/'); // lien absolu racine du projet
// Chemin racine du serveur
define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT']);








