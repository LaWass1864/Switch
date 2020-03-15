<?php 
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

// si l'utilisateur est connecté, on le renvoie sur la page profil
if(user_is_connect()) {
	header('location:profil.php');
}


// Declaration des variables 

$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';	
$date_enregistrement='';

if(isset($_POST['pseudo']) 
&& isset($_POST['mdp'])
&& isset($_POST['nom'])
&& isset($_POST['prenom'])
&& isset($_POST['email'])
&& isset($_POST['civilite'])
){


$pseudo = trim($_POST['pseudo']);
$mdp = trim($_POST['mdp']);
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$email = trim($_POST['email']);
$civilite = trim($_POST['civilite']);
		
		
		
		// on bloque certains caractères pour le champ pseudo via une expression régulière (regex). On autorise uniquement a-z A-Z 0-9 -._ 
		$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
		/*
		preg_match() est une fonction prédéfinie permettant de vérifier une chaine fournie en deuxième argument selon une expression régulière fournie en premier argument. Renvoie 1 si c'est ok sinon 0
		
		- les # indiquent le début et la fin de l'expression régluière
		- Entre les [] se trouvent tous les caractères autorisés.
		- ^ indique que le début de la chaine ne peut pas commencer par un autre caractère que ceux présent dans les []
		- $ indique que la fin de la chaine ne peut pas finir par un autre caractère que ceux présent dans les []
		- Le + permet d'indiquer que les caractères peuvent être présent plusieurs fois.		
		
		*/
		
		if(!$verif_caractere && !empty($pseudo)) {
			// cas d'erreur
			$msg .= '<div class="alert alert-danger mt-3">Pseudo invalide, caractères autorisés : a-z et de 0-9</div>';			
		}
		
		// vérifier la taille du pseudo => message d'erreur si le pseudo n'est pas entre 4 et 14 caractères inclus.
		if(iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14) {
			// cas d'erreur
			$msg .= '<div class="alert alert-danger mt-3">Pseudo invalide, le pseudo doit avoir entre 4 et 14 caractères inclus</div>';	
		}
		
		// mettre en place un controle sur la validité du format de l'email
		
		
		// s'il n'y pas eu d'erreur au préalable, on doit vérifier si le pseudo existe déjà dans la BDD
		if(empty($msg)) {
			// si la variable $msg est vide, alors il n'y a pas eu d'erreur dans nos controles.
			
			// on vérifie si le pseudo est disponible.
			$verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
			$verif_pseudo->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
			$verif_pseudo->execute();
			
			if($verif_pseudo->rowCount() > 0) {
				// si le nombre de ligne est supérieur à zéro, alors le pseudo est déjà utilisé.
				$msg .= '<div class="alert alert-danger mt-3">Pseudo indisponible !</div>';	
			} else {
				// insert into
				// cryptage du mot de passe pour l'insertion en BDD
				$mdp = password_hash($mdp, PASSWORD_DEFAULT);
				
				// On déclenche l'insertion
			
					$mdp = password_hash($mdp, PASSWORD_DEFAULT);
					$enregistrement = $pdo->prepare("INSERT INTO membre(id_membre, pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (NULL, :pseudo, :mdp, :nom, :prenom, :email, :civilite, 0, NOW())");
					$enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
					$enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
					$enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
					$enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
					$enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
					$enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
					$enregistrement->execute();
				}

		}		
}



include 'inc/head.inc.php';
include 'inc/nav.inc.php';

?>

	<div class="starter-template text-center mt-5">
		<h1><i style="color: #4c6ef5;"></i> Inscription <i style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>
	</div>

	<div class="row">
		<div class="col-12">
		<form method ='post' action="">
    		<!-- <?php // echo '<pre>'; var_dump($_POST); echo '</pre>'; ?> -->
    		<div class='row'>
    			<div class='col-6'>
    				<div class="form-group">
    					<label for="pseudo">Pseudo</label>
    					<input type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo ?>" class="form-control">
    				</div>
    				<div class="form-group">
    					<label for="mdp">Mot de Passe</label>
    					<input type="password" name="mdp" id="mdp" value="" class="form-control">
    				</div>
    				<div class="form-group">
    					<label for="nom">Nom</label>
    					<input type="text" name="nom" id="nom" value="<?php echo $nom ?>" class="form-control">
    				</div>
    			</div>
    			<div class='col-6'>
    				<div class="form-group">
    					<label for="prenom">Prénom</label>
    					<input type="text" name="prenom" id="prenom" value="<?php echo $prenom ?>" class="form-control">
    				</div>
    				<div class="form-group">
    					<label for="email">Email</label>
    					<input type="text" name="email" id="email" value="<?php echo $email ?>" class="form-control">
    				</div>
	    			<div class="form-group">
    					<label for="civilite">Civilité</label>
    					<select name="civilite" id="civilite" class="form-control">
    						<option value="m">Homme</option>
    						<option value="f" <?php if($civilite == 'f') { echo 'selected'; }?> >Femme</option>
    					</select>
	    			</div>
    			</div>
    			<div class='col-12'>
    				<div class="form-group">
						<input class="form-control btn btn-outline-primary mx-auto" type="submit" id="enregistrement" value="Enregistrement">
    				</div>
    			</div>
    		</div>
    	</form>
	</div>
  </div>

	<?php 
include 'inc/footer.inc.php';