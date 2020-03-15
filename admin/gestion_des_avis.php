<?php 
include '../inc/init.inc.php';
include '../inc/fonction.inc.php';

	// code 

	if(!user_is_admin()) {
		header('location:' . URL . 'connexion.php');
		exit(); // bloque l'exécution du code 
		}
	

		$recup_avis = $pdo -> query("SELECT id_avis, id_membre, id_salle, commentaire, note, DATE_FORMAT(date_enregistrement, '%d/%m/%Y %H:%m') as date_enregistrement FROM avis");
		$avis = $recup_avis -> fetchAll(PDO::FETCH_ASSOC);
	
		if ($_POST) {
			if (!empty($_POST['commentaire']) && strlen(trim($_POST['commentaire'])) > 0) {
				if (isset($_GET['id'])) {
					$modif = $pdo->prepare("UPDATE avis SET commentaire = :commentaire WHERE id_avis = :id");
					$modif->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
				}
		
				$modif->bindParam(':commentaire', $_POST['commentaire'], PDO::PARAM_STR);
		
				if ($modif->execute()) {
					header('location:gestion_avis.php');
				}
			} else {
				$msg .= '<div class="erreur">Veuillez remplir le champs commentaire !</div>';
			}
			
			
		}
	
	if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_avis'])) {
		$suppression = $pdo->prepare("DELETE FROM avis WHERE id_avis = :id_avis");
		$suppression->bindParam(":id_avis", $_GET['id_avis'], PDO::PARAM_STR);
		$suppression->execute();
		
		$_GET['action'] = 'affichage'; // pour provoquer l'affichage du tableau
		
	}
	
	
	$id_avis ='';
	$id_membre= ''; 
	$id_salle= ''; 
	$commentaire= ''; 
	$note= '';
	$date_enregistrement= '';  
	$liste_avis="";

	
	//*********************************************************************
	//*********************************************************************
	// ENREGISTREMENT & MODIFICATION DES salles
	//*********************************************************************
	//*********************************************************************
	if(
		//isset($_POST['id_produit']) &&
		//isset($_POST['id_salle']) &&
		//isset($_POST['id_membre']) &&
		isset($_POST['commentaire']) &&
		isset($_POST['note']) &&		
		isset($_POST['date_enregistrement']) ) {
			
			$id_avis = trim($_GET['id_avis']);
			$id_membre = trim($_GET['id_membre']);
			$id_salle = trim($_GET['id_salle']);
			$commentaire = trim($_POST['commentaire']);
			$note = trim($_POST['note']);
			$date_depart = trim($_POST['date_enregistrement']);
								
						
			
			// controle sur la référence car elle est unique en BDD
			$verif_reference = $pdo->prepare("SELECT * FROM avis WHERE id_avis = :reference");
			$verif_reference->bindParam(':reference', $reference, PDO::PARAM_STR);
			$verif_reference->execute();		
			
			
			// si on a une ligne, alors la reference existe en bdd
			// on ne vérifie la référence que lors d'un ajout. Si id_avis est vide alors c'est un ajout sinon c'est une modif.
			if($verif_reference->rowCount() > 0 && empty($id_avis)) {
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
						
						// pour ne pas d'écraser une image du même nom, on renomme l'image en rajoutant la référence qui est une information unique
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
					$enregistrement = $pdo->prepare("UPDATE avis SET  commentaire  = :commentaire, note = :note, date_enregistrement = :date_enregistrement, WHERE id_avis = :id_avis");
					// on rajoute le bindParam pour l'id_produit car => modification
					$enregistrement->bindParam(":id_avis", $id_avis, PDO::PARAM_STR);
					
				} else {
					// sinon un INSERT
					$enregistrement = $pdo->prepare("INSERT INTO avis (commentaire ,note, date_enregistrement,) VALUES (commentaire  = :commentaire, note = :note, date_enregistrement = :date_enregistrement,)");
				}
				
				
				
				$enregistrement->bindParam(":id_avis", $id_avis, PDO::PARAM_STR);
				$enregistrement->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
				$enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);				
				$enregistrement->bindParam(":commentaire", $commentaire, PDO::PARAM_STR);			
				$enregistrement->bindParam(":note", $note, PDO::PARAM_STR);
				$enregistrement->bindParam(":date_enregistrement", $date_enregistrement, PDO::PARAM_STR);
				$enregistrement->execute();
				
				
			}
		}				
	
	//*********************************************************************
	//*********************************************************************
	// \FIN ENREGISTREMENT DES avis
	//*********************************************************************
	//*********************************************************************
	

include '../inc/head.inc.php';
include '../inc/nav.inc.php';
?>


<div class="starter-template text-center mt-5">
		<h1><i  style="color: #4c6ef5;"></i> Gestion des avis <i  style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>
</div>


	<div class="row">
		<div class="col-12">	


						
<?php
//***************************
		// AFFICHAGE DES AVIS
		//***************************
		$liste_avis = $pdo->query("SELECT * FROM avis");			
		$liste_avis->rowCount() . '</b></p>';	
					
			echo '<div class="table-responsive">
			<table class="table table-bordered">
			<tr>
			<th>id avis</th>
			<th>id membre</th>
			<th>id salle</th>
			<th>commentaire</th>
			<th>note</th>
			<th>date_enregistrement</th>';
					
			echo '<th>Modif</th>';
			echo '<th>Suppr</th>';
			echo '</tr>';
			
			while($avis = $liste_avis->fetch(PDO::FETCH_ASSOC)) {
				echo '<tr>';
				echo '<td>' . $avis['id_avis'] . '</td>';				
				echo '<td>' . $avis['id_membre'] . '</td>';
				echo '<td>' . $avis['id_salle'] . '</td>';
				echo '<td>' . substr($avis['commentaire'], 0, 50) . ' ...</td>';
				echo '<td>' . $avis['note'] . '</td>';
				echo '<td>' . $avis['date_enregistrement'] . '</td>';
				
				
echo '<td><a href="?action=modifier&id_avis=' . $avis['id_avis'] . '" class="btn btn-warning"><i class="fas fa-edit"></i></a></td>';

echo '<td><a href="?action=supprimer&id_avis=' . $avis['id_avis'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="fas fa-trash-alt"></i></a></td>';
				
				echo '</tr>';
			}
			
			echo '</table>';
			echo '</div>';
			
					
	
	//***************************
	// FIN AFFICHAGE DES AVI
	//***************************
	?>
</div>
</div>		


 


<?php 


include '../inc/footer.inc.php';