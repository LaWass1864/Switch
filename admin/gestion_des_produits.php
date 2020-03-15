<?php 
include '../inc/init.inc.php';
include '../inc/fonction.inc.php';

	// code ...

	if(!user_is_admin()) {
		header('location:' . URL . 'connexion.php');
		exit(); // bloque l'exécution du code 
		}
	
	// declaration des variables
	
	if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_produit'])) {
		$suppression = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
		$suppression->bindParam(":id_produit", $_GET['id_produit'], PDO::PARAM_STR);
		$suppression->execute();
		
		$_GET['action'] = 'affichage'; // pour provoquer l'affichage du tableau
		
	}
	
	//*********************************************************************
	//*********************************************************************
	// \FIN SUPPRESSION D'UNE salle
	//*********************************************************************
	//*********************************************************************
	$id_produit ='';
	$id_salle = ''; // pour la modification
	$date_arrivee = '';
	$date_depart = '';
	$prix = '';
	$etat = '';	
	
	
	//*********************************************************************
	//*********************************************************************
	// ENREGISTREMENT & MODIFICATION DES PRODUITS
	//*********************************************************************
	//*********************************************************************
	if(
		isset($_POST['id_produit']) &&
		isset($_POST['id_salle']) &&
		isset($_POST['date_arrivee']) &&
		isset($_POST['date_depart']) &&
		isset($_POST['prix']) &&		
		isset($_POST['etat']) ) {
			
			$id_produit = trim($_GET['id_produit']);
			$id_salle = trim($_GET['id_salle']);
			$date_arrivee = trim($_POST['date_arrivee']);
			$date_depart = trim($_POST['date_depart']);
			$prix = trim($_POST['prix']);
			$etat = trim($_POST['etat']);
		
			
			// récupération de la photo actuelle pour les modifs
			if(!empty($_POST['photo_actuelle'])) {
				$photo_bdd = $_POST['photo_actuelle'];
			}			
			
			
			if(empty($prix) || !is_numeric($prix)) {
				$msg .= '<div class="alert alert-danger mt-3">Attention, le prix est obligatoire et doit être numérique.</div>';
			}
			
			if($etat !== 0 && !is_numeric($etat)) {
				$msg .= '<div class="alert alert-danger mt-3">Attention, l\'etat est obligatoire et doit être numérique.</div>';
			}
			
			// controle sur la référence car elle est unique en BDD
			$verif_reference = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :reference");
			$verif_reference->bindParam(':reference', $reference, PDO::PARAM_STR);
			$verif_reference->execute();		
			
			
			// si on a une ligne, alors la reference existe en bdd
			// on ne vérifie la référence que lors d'un ajout. Si id_salle est vide alors c'est un ajout sinon c'est une modif.
			if($verif_reference->rowCount() > 0 && empty($id_produit)) {
				$msg .= '<div class="alert alert-danger mt-3">Attention, référence indisponible car déjà attribuée.</div>';
			} else {
				// vérification du format de l'image, formats accèptés : jpg, jpeg, png, gif
				// est-ce qu'une image a été posté : 
				if(!empty($_FILES['photo']['name'])) {
					
					// on vérifie le format de l'image en récupérant son extension
					$extension = strrchr($_FILES['photo']['name'], '.');
					// strrchr() découpe une chaine fournie en premier argument en partant de la fin. On remonte jusqu'au caractère fourni en deuxième argument et on récupère tout depuis ce caractère.
					// exemple strrchr('image.png', '.'); => on récupère .png
					// var_dump($extension);
					
					// on enlève le point et on passe l'extension en minuscule pour pouvoir la comparer.
					$extension = strtolower(substr($extension, 1));
					// exemple : .PNG => png    .Jpeg => jpeg
					
					// on déclare un tableau array contenant les extensions autorisées :
					$tab_extension_valide = array('png', 'gif', 'jpg', 'jpeg');
					
					// in_array(ce_quon_cherche, tableau_ou_on_cherche);
					// in_array() renvoie true si le premier argument correspond à une des valeurs présentes dans le tableau array fourni en deuxième argument. Sinon false
					$verif_extension = in_array($extension, $tab_extension_valide);
					
					if($verif_extension) {
						
						// pour ne pasd écraser une image du même nom, on renomme l'image en rajoutant la référence qui est une information unique
						$nom_photo = $reference . '-' .  $_FILES['photo']['name'];
						
						$photo_bdd = $nom_photo; // représente l'insertion en BDD
						
						// on prépare le chemin où on va enregistrer l'image
						$photo_dossier = SERVER_ROOT . SITE_ROOT . 'photo/' . $nom_photo;
						// var_dump($photo_dossier);
						
						// copy(); permet de copier un fichier depuis un emplacement fourni en premier argument vers un emplacement fourni en deuxième
						copy($_FILES['photo']['tmp_name'], $photo_dossier);
						
						
					} else {
						$msg .= '<div class="alert alert-danger mt-3">Attention, le format de la photo est invalide, extensions autorisées : jpg, jpeg, png, gif.</div>';
					}
					
				}
				
			}
			
			// on peut déclencher l'enregistrement s'il n'y a pas eu d'erreur dans les traitements précédents
			if(empty($msg)) {
				
				if(!empty($id_produit)) {
					// si $id_produit n'est pas vide c'est un UPDATE
					$enregistrement = $pdo->prepare("UPDATE produit SET  date_depart  = :date_depart, date_arrivee = :date_arrivee, prix = :prix, etat = :etat WHERE id_produit = :id_produit");
					// on rajoute le bindParam pour l'id_produit car => modification
					$enregistrement->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
					
				} else {
					// sinon un INSERT
					$enregistrement = $pdo->prepare("INSERT INTO produit (date_depart ,date_arrivee, prix, etat) VALUES (date_depart  = :date_depart, date_arrivee = :date_arrivee, prix = :prix, etat = :etat)");
				}
				
				
				
				$enregistrement->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
				$enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
				$enregistrement->bindParam(":date_depart", $date_depart, PDO::PARAM_STR);
				$enregistrement->bindParam(":date_arrivee", $date_arrivee, PDO::PARAM_STR);			
				$enregistrement->bindParam(":prix", $prix, PDO::PARAM_STR);
				$enregistrement->bindParam(":etat", $etat, PDO::PARAM_STR);
				$enregistrement->execute();
				
				
			}
		}				
	
	//*********************************************************************
	//*********************************************************************
	// \FIN ENREGISTREMENT DES SALLES
	//*********************************************************************
	//*********************************************************************
	
	
	//*********************************************************************
	//*********************************************************************
	// MODIFICATION : RECUPERATION DES INFOS DE la salle EN BDD
	//*********************************************************************
	//*********************************************************************
	if(isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_produit'])) {
		
		$infos_salle = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
		$infos_salle->bindparam(":id_produit", $_GET['id_produit'], PDO::PARAM_STR);
		$infos_salle->execute();
		
		if($infos_salle->rowCount() > 0) {
			$salle_actuel = $salle_actuel->fetch(PDO::FETCH_ASSOC);			
			$id_produit = $salle_actuel['id_produit']; 
			$date_arrivee = $salle_actuel['date_arrivee'];
			$date_depart = $salle_actuel['date_depart'];
			$prix = $salle_actuel['prix'];
			$etat = $salle_actuel['etat'];
		
		}
	}
	


include '../inc/head.inc.php';
include '../inc/nav.inc.php';

?>

<div class="starter-template text-center mt-5">
		<h1><i  style="color: #4c6ef5;"></i> Gestion des produits des salles <i  style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>

</div>		


<div class="row">
		<div class="col-12">

<?php
//***************************
		// AFFICHAGE DES ARTICLES
		//***************************
		
			// on récupère les articles en bdd
			$liste_produit = $pdo->query("SELECT * FROM produit, salle WHERE salle.id_salle = produit.id_salle");			
			$liste_produit->rowCount() . '</b></p>';
			
			echo '<div class="table-responsive">';
			echo '<table class="table table-bordered">';
			echo '<tr>';
			echo '<th>Id produit</th>';
			echo '<th>date d\'arrivée</th>';
			echo '<th>date depart</th>';
			echo '<th>Id salle</th>';
			echo '<th>Photo</th>';
			echo '<th>Prix</th>';
			echo '<th>Etat</th>';	
			echo '<th>Modif</th>';
			echo '<th>Suppr</th>';
			echo '</tr>';
			
			while($produit = $liste_produit->fetch(PDO::FETCH_ASSOC)) {
				echo '<tr>';
				echo '<td>' . $produit['id_produit'] . '</td>';
				echo '<td>' . $produit['date_arrivee'] . '</td>';
				echo '<td>' . $produit['date_depart'] . '</td>';
				echo '<td>' . $produit['id_salle'] . '</td>';	
				echo '<td><img src="' . URL . 'photo/' . $produit['photo'] . '" class="img-thumbnail" width="140"></td>';
				echo '<td>' . $produit['prix'] . '</td>';
				echo '<td>' . $produit['etat'] . '</td>';		
										
								
				
echo '<td><a href="?action=modifier&id_salle=' . $produit['id_produit'] . '" class="btn btn-warning">
<i class="fas fa-edit"></i></a></td>';

echo '<td><a href="?action=supprimer&id_salle=' . $produit['id_produit'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))">
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
	
	
		<?php
	// récupération de la photo de la salle en cas de modification. Pour la consever si l'utilisateur n'en charge pas une nouvelle
	if(!empty($photo_actuelle)) {
		echo '<div class="form-group text-center">';
		echo '<label>Photo actuelle</label><hr>';
		echo '<img src="' . URL . '/photo/' . $photo_actuelle . '" class="w-25 img-thumbnail" alt="image de la salle">';
		echo '<input type="hidden" name="photo_actuelle" value="' . $photo_actuelle . '">';
		echo '</div>';
	}
	?>	

<form method="post" action="" enctype="multipart/form-data">
			<!-- récupération de l'id_article pour la modification -->
			<input type="hidden" name="id_produit" value="<?php echo $id_produit; ?>">

<div class="row">	
	<label for="date-picker-example">Date d'arrivée</label>
	 <!-- <input placeholder="Selected date" type="text" id="date-picker-example" class="form-control datepicker">  -->
	 <input type ="date" name="date_arrivee" class="form-control">
	 <hr>
	 <label for="date-picker-example">Date de depart</label>
	 <!-- <input placeholder="Selected date" type="text" id="date-picker-example" class="form-control datepicker">  -->
	 <input type ="date" name="date_depart" class="form-control">
</div> 


	 <hr>
	 <div class="row">
<div class="input-group">

  <div class="input-group-prepend">  
    <span class="input-group-text" id="basic-addon1">€</span>
  </div>
  <input type="text" class="form-control" placeholder="prix en euros" aria-label="Username" aria-describedby="basic-addon1">
</div>

<div class="form-group">
    <label for="exampleFormControlSelect1">Salle</label>
    <select class="form-control" id="exampleFormControlSelect1">
      <option <?php if($salle == '1') { echo 'selected'; } ?>>1</option>
      <option <?php if($salle == '2') { echo 'selected'; } ?>>2</option>
      <option <?php if($salle == '3') { echo 'selected'; } ?>>3</option>
      <option <?php if($salle == '4') { echo 'selected'; } ?>>4</option>
      <option <?php if($salle == '5') { echo 'selected'; } ?>>5</option>
    </select>
  </div>
</div> 

  <div class="form-group">
	<button type="submit" name="enregistrement" id="enregistrement" class="form-control btn btn-outline-dark"> Enregistrement </button>
</div>


  <!-- Faire une requete pour les dates d'arrivée et de depart. -->




	</div>
	</div>
<?php

include '../inc/footer.inc.php';