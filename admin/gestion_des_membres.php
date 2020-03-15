<?php 
include '../inc/init.inc.php';
include '../inc/fonction.inc.php';

	// code ...
	if(!user_is_admin()) {
		header('location:' . URL . 'connexion.php');
		exit(); // bloque l'exécution du code 
		}
	
		// récuperation des membre depuis la BDD

	$recup_membre = $pdo -> query("SELECT id_membre, pseudo, nom, prenom, email, civilite, statut, DATE_FORMAT(date_enregistrement, '%d/%m/%Y %H:%m') as date_enregistrement FROM membre");

	$membre = $recup_membre -> fetchAll(PDO::FETCH_ASSOC);	
	
	// declaration des variables
	
	if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_produit'])) {
		$suppression = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
		$suppression->bindParam(":id_membre", $_GET['id_produit'], PDO::PARAM_STR);
		$suppression->execute();
		
		$_GET['action'] = 'affichage'; // pour provoquer l'affichage du tableau
		
	}
	
	//*********************************************************************
	//*********************************************************************
	// \FIN SUPPRESSION D'UN MEMBRE
	//*********************************************************************
	//*********************************************************************
	$id_membre ='';
	$pseudo = ''; // pour la modification	
	$prenom = '';
	$nom = '';
	$email = '';	
	$civilite = '';
	$statut = '';
	$date_enregistrement = '';
	$actions = '';

	
	//*********************************************************************
	//*********************************************************************
	// ENREGISTREMENT & MODIFICATION DES salles
	//*********************************************************************
	//*********************************************************************
	if(
		//isset($_POST['id_produit']) &&
		//isset($_POST['id_salle']) &&
		isset($_POST['pseudo']) &&
		isset($_POST['prenom']) &&
		isset($_POST['nom']) &&	
		isset($_POST['civilite']) &&	
		isset($_POST['statut']) &&		
		isset($_POST['email']) ) {
			
			$id_membre = trim($_GET['id_membre']);
			$pseudo = trim($_POST['pseudo']);
			$prenom = trim($_POST['prenom']);
			$prix = trim($_POST['prix']);
			$civilite = trim($_POST['civilite']);
			$statut = trim($_POST['statut']);
			$email = trim($_POST['email']);		
								
			
			
			
			// controle sur la référence car elle est unique en BDD
			$verif_reference = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
			$verif_reference->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
			$verif_reference->execute();		
			
			
			
			
			// on peut déclencher l'enregistrement s'il n'y a pas eu d'erreur dans les traitements précédents
			if(empty($msg)) {
				
				if(!empty($id_membre)) {
					// si $id_produit n'est pas vide c'est un UPDATE
					$enregistrement = $pdo->prepare("UPDATE membre SET  pseudo  = :pseudo, mdp = :mdp, nom = :nom, prenom = :prenom, email = :email, statut = :statut, 
					WHERE id_membre = :id_membre");
					// on rajoute le bindParam pour l'id_produit car => modification
					$enregistrement->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
					
				} else {
					// sinon un INSERT
					$enregistrement = $pdo->prepare("INSERT INTO membre (pseudo ,mdp, nom, prenom,email, statut) 
					VALUES (pseudo  = :pseudo, mdp = :mdp, nom = :nom, prenom = :prenom, email = :email, statut = :statut)");
				}
								
				
				$enregistrement->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
				$enregistrement->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
				$enregistrement->bindParam(":mdp", $mdp, PDO::PARAM_STR);
				$enregistrement->bindParam(":nom", $nom, PDO::PARAM_STR);			
				$enregistrement->bindParam(":prenom", $prenom, PDO::PARAM_STR);
				$enregistrement->bindParam(":email", $email, PDO::PARAM_STR);
				$enregistrement->bindParam(":statut", $statut, PDO::PARAM_STR);

				$enregistrement->execute();
				
				
			}
		}				
	
	//*********************************************************************
	//*********************************************************************
	// \FIN ENREGISTREMENT DES MEMBRES
	//*********************************************************************
	//*********************************************************************
	
	
	//*********************************************************************
	//*********************************************************************
	// MODIFICATION : RECUPERATION DES INFOS DU MEMBRE EN BDD
	//*********************************************************************
	//*********************************************************************
	if(isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_membre'])) {
		
		$liste_membre = $pdo->prepare("SELECT * FROM membre  WHERE id_membre  = :id");
		$liste_membre->bindparam(":id_membre", $_GET['id_membre'], PDO::PARAM_STR);
		$liste_membre->execute();
		
		if($liste_membre->rowCount() > 0) {
			$id_membre = $liste_membre->fetch(PDO::FETCH_ASSOC);			
			$id_membre = $liste_membre['id_membre']; 
			$date_arrivee = $liste_membre['date_arrivee'];
			$date_depart = $liste_membre['date_depart'];
			$prix = $liste_membre['prix'];
			$etat = $liste_membre['etat'];
		
		}
	}

include '../inc/head.inc.php';
include '../inc/nav.inc.php';

?>

<div class="starter-template text-center mt-5">
<h1><i style="color: #4c6ef5;"></i> Backoffice/ Gestion des membres <i style="color: #4c6ef5;"></i></h1>
<p class="lead"><?php echo $msg; ?></p>

</div>		


<div class="row">
<div class="col-12">

<?php
//***************************
// AFFICHAGE DES ARTICLES
//***************************

	// on récupère les articles en bdd
	$liste_membre = $pdo->query("SELECT * FROM membre");			
	$liste_membre->rowCount() . '</b></p>';
	
	echo '<div class="table-responsive">';
	echo '<table class="table table-bordered">';
	echo '<tr>';
	echo '<th>Id membre</th>';
	echo '<th>Pseudo</th>';
	echo '<th>Nom</th>';
	echo '<th>Prénom</th>';
	echo '<th>Email</th>';
	echo '<th>Civilité</th>';
	echo '<th>Statut</th>';	
	echo '<th>Date d\'enregistrement</th>';			
				
	echo '<th>Modif</th>';
	echo '<th>Suppr</th>';
	echo '</tr>';
	
	while($membre = $liste_membre->fetch(PDO::FETCH_ASSOC)) {
		echo '<tr>';
		echo '<td>' . $membre['id_membre'] . '</td>';
		echo '<td>' . $membre['pseudo'] . '</td>';
		echo '<td>' . $membre['nom'] . '</td>';
		echo '<td>' . $membre['prenom'] . '</td>';	
		echo '<td>' . $membre['email'] . '</td>';	
		echo '<td>' . $membre['civilite'] . '</td>';	
		echo '<td>' . $membre['statut'] . '</td>';
		echo '<td>' . $membre['date_enregistrement'] . '</td>';		
								
						
		
echo '<td><a href="?action=modifier&id_membre=' . $membre['id_membre'] . '" class="btn btn-warning"><i class="fas fa-edit"></i></a></td>';

echo '<td><a href="?action=supprimer&id_membre=' . $membre['id_membre'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))">
<i class="fas fa-trash-alt"></i></a></td>';
		
		echo '</tr>';
	}
	
	echo '</table>';
	echo '</div>';
	
	

//***************************
// FIN AFFICHAGE DES ARTICLES
//***************************
?>
</div>
<div class="col-12">


<form method="post" action="" enctype="multipart/form-data">
	<!-- récupération de l'id_article pour la modification -->
	<input type="hidden" name="id_produit" value="<?php echo $id_membre; ?>">

	<div class="input-group mb-3">
  <div class="input-group-prepend">	  
    <span class="input-group-text" id="basic-addon1">Pseudo</span>
  </div>
  <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
</div>


<hr>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Mot de passe</span>
  </div>
  <input type="text" class="form-control" placeholder="Mot de passe" aria-label="Username" aria-describedby="basic-addon1">
</div>

<hr>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Nom</span>
  </div>
  <input type="text" class="form-control" placeholder="Nom" aria-label="Username" aria-describedby="basic-addon1">
</div>

<hr>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Prenom</span>
  </div>
  <input type="text" class="form-control" placeholder="Prenom" aria-label="Username" aria-describedby="basic-addon1">
</div>

<hr>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Email</span>
  </div>
  <input type="text" class="form-control" placeholder="Email" aria-label="Username" aria-describedby="basic-addon1">
</div>

<hr>

<div class="form-group">
    <label for="exampleFormControlSelect1">Civilité</label>
    <select class="form-control" id="exampleFormControlSelect1">
      <option>Femme</option>
      <option>Homme</option>
     
    </select>
  </div>



</form>



<!-- Faire une requete pour les dates d'arrivée et de depart. -->




</div>
</div>



 

<!-- /.row -->
<?php 
include '../inc/footer.inc.php';