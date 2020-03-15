<?php 
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

// déconnexion
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
	session_destroy(); // je détruis la session pour provoquer la déconnexion.
}


// si l'utilisateur est connecté, on le renvoie sur la page profil
if(user_is_connect()) {
	header('location:profil.php');
}

$pseudo = '';
// est ce que le formulaire a été validé
if(isset($_POST['pseudo']) && isset($_POST['mdp'])) {
	$pseudo = trim($_POST['pseudo']);
	$mdp = trim($_POST['mdp']);
	
	// on récupère les informations en bdd de l'utilisateur sur la base du pseudo (unique en bdd)
	$verif_connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
	$verif_connexion->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
	$verif_connexion->execute();
	
	if($verif_connexion->rowCount() > 0) {
		// s'il y a une ligne dans $verif_connexion alors le pseudo est bon
		$infos = $verif_connexion->fetch(PDO::FETCH_ASSOC);
		// echo '<pre>'; var_dump($infos); echo '</pre>';
		
		// on compare le mot de passe qui a été crypté avec password_hash() via la fonction prédéfinie pasword_verify()
		// if($mdp == $infos['mdp'])
		// echo $mdp . ' : ' . $infos['mdp'];
		if(password_verify($mdp, $infos['mdp']))
		
		{
			// le pseudo et le mot de passe sont corrects, on enregistre les informations du membre dans la session 
			
			$_SESSION['membre'] = array();
			
			$_SESSION['membre']['id_membre'] = $infos['id_membre'];
			$_SESSION['membre']['pseudo'] = $infos['pseudo'];
			$_SESSION['membre']['nom'] = $infos['nom'];
			$_SESSION['membre']['prenom'] = $infos['prenom'];
			$_SESSION['membre']['civilite'] = $infos['civilite'];
			$_SESSION['membre']['email'] = $infos['email'];
			$_SESSION['membre']['statut'] = $infos['statut'];
			$_SESSION['membre']['mdp'] = $infos['statut'];
			
			
			
			// maintenant que l'utilisateur est connecté, on le redirige vers profil.php c'est le chemin vers le profil
			header('location:profil.php');
			// header('location:...) doit être exécuté AVANT le moindre affichage dans la page sinon => bug
			
			
		} else {
			$msg .= '<div class="alert alert-danger mt-3">Erreur sur le pseudo et / ou le mot de passe !</div>';	
		}
		
	} else {
		$msg .= '<div class="alert alert-danger mt-3">Erreur sur le pseudo et / ou le mot de passe !</div>';	
	}
	
}




include 'inc/head.inc.php';
include 'inc/nav.inc.php';

echo '<pre>'; var_dump($_SESSION); echo '</pre>';
?>

	<div class="starter-template text-center mt-5">
		<h1><i style="color: #4c6ef5;"></i> Connexion <i style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>
	</div>

	<div class="row">
		<div class="col-4 mx-auto">
		
<form method="post" action="">
	<div class="form-group">
		<label for="pseudo">Pseudos</label>
		<input type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo; ?>" class="form-control">
	</div>
	<div class="form-group">
		<label for="mdp">Mot de passe</label>
		<input type="password" autocomplete="off" name="mdp" id="mdp" value="" class="form-control">
	</div>
	<div class="form-group">
		<button type="submit" name="connexion" id="connexion" class="form-control btn btn-outline-success"> Connexion </button>
	</div>
</form>			
			
		</div>
	</div>


<?php 
include 'inc/footer.inc.php';