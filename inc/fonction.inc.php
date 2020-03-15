<?php
// fonction pour savoir si l'utilisateur est connecté
function user_is_connect() {
	if(!empty($_SESSION['membre'])) {
		return true; // utilisateur est connecté
	}
	return false; // utilisateur n'est pas connecté
}


// fonction pour savoir si l'utilisateur a le statut d'admin
function user_is_admin() {
	if(user_is_connect() && $_SESSION['membre']['statut'] == 1) {
		// si l'utilisateur est connecté et que son statut est égal à 2 alors il est admin
		return true;
	} else {
		return false;
	}
}
