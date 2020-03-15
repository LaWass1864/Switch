<?php

include '../inc/init.inc.php';
include '../inc/fonction.inc.php';

	// code 



if(!user_is_admin()) {
	header('location:' . URL . 'connexion.php');
	exit(); // bloque l'exécution du code
	}

// declaration des variables

if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_salle'])) {
	$suppression = $pdo->prepare("DELETE FROM salle WHERE id_salle = :id_salle");
	$suppression->bindParam(":id_salle", $_GET['id_salle'], PDO::PARAM_STR);
	$suppression->execute();

	$_GET['action'] = 'affichage'; // pour provoquer l'affichage du tableau

}

//*********************************************************************
//*********************************************************************
// \FIN SUPPRESSION D'UNE salle
//*********************************************************************
//*********************************************************************
$id_salle = ""; // pour la modification
$description = "";
$titre = "";
$pays = "";
$ville = "";
$adresse = "";
$cp = "";
$photo_bdd = "";
$capacite = "";
$categorie = "";
$actions = "";
$reference ="";




//*********************************************************************
//*********************************************************************
// ENREGISTREMENT & MODIFICATION DES salles
//*********************************************************************
//*********************************************************************

// var_dump ($_POST);

if (
	//isset($_POST['id_salle']) &&
	isset($_POST['description']) &&
	isset($_POST['titre']) &&
	isset($_POST['pays']) &&
	isset($_POST['ville']) &&
	isset($_POST['adresse']) &&
	isset($_POST['cp']) &&
	isset($_POST['capacite']) &&
	isset($_POST['categorie']))  {



		//$id_salle = trim($_GET['id_salle']); // get = enregistre la variable qui vient de l'URL pour le bouton modifier
		$description = trim($_POST['description']);
		$titre = trim($_POST['titre']);
		$pays = trim($_POST['pays']);
		$ville = trim($_POST['ville']);
		$adresse = trim($_POST['adresse']);
		$cp = trim($_POST['cp']);
		$capacite = trim($_POST['capacite']);
		$categorie = trim($_POST['categorie']);


		// récupération de la photo actuelle pour les modifs
		if(!empty($_POST['photo_actuelle'])) {
			$photo_bdd = $_POST['photo_actuelle'];
		}



		if(empty($cp) || !is_numeric($cp)) {
			$msg .= '<div class="alert alert-danger mt-3">Attention, le code postal est obligatoire et doit être numérique.</div>';
		}

		if(empty($capacite) && !is_numeric($capacite)) {
			$msg .= '<div class="alert alert-danger mt-3">Attention, le stock est obligatoire et doit être numérique.</div>';
		}

		// controle sur la référence car elle est unique en BDD
		$verif_reference = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
		$verif_reference->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
		$verif_reference->execute();


		// si on a une ligne, alors la reference existe en bdd
		// on ne vérifie la référence que lors d'un ajout. Si id_salle est vide alors c'est un ajout sinon c'est une modif.
		if($verif_reference->rowCount() > 0 && empty($id_salle)) {
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

					// pour ne pas écraser une image du même nom, on renomme l'image en rajoutant la référence qui est une information unique
					$nom_photo = $reference . $_FILES['photo']['name'];

					$photo_bdd = $nom_photo; // représente l'insertion en BDD

					// on prépare le chemin où on va enregistrer l'image
					$photo_dossier = SERVER_ROOT . '/photo/' . $nom_photo;
					var_dump($photo_dossier);

					// copy(); permet de copier un fichier depuis un emplacement fourni en premier argument vers un emplacement fourni en deuxième
					copy($_FILES['photo']['tmp_name'], $photo_dossier);


				} else {
					$msg .= '<div class="alert alert-danger mt-3">Attention, le format de la photo est invalide, extensions autorisées : jpg, jpeg, png, gif.</div>';
				}

			}

		}

		// on peut déclencher l'enregistrement s'il n'y a pas eu d'erreur dans les traitements précédents
		if(empty($msg)) {

			if(!empty($id_salle)) {
				// si $id_salle n'est pas vide c'est un UPDATE
				$enregistrement = $pdo->prepare("UPDATE salle SET description = :description, titre = :titre, categorie = :categorie , pays = :pays,ville = :ville ,adresse = :adresse,cp = :cp, capacite = :capacite , photo = :photo WHERE id_salle = :id_salle"); //modif la salle qui est ds GET, celle qui est modifié.
				// on rajoute le bindParam pour l'id_salle car => modification + quelles ligne selectionnée
				$enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);

			} else {
				// sinon un INSERT
				$enregistrement = $pdo->prepare("INSERT INTO salle (description,titre,categorie ,pays,ville,adresse,cp,capacite,photo)
				 VALUES (:description,:titre,:categorie ,:pays,:ville,:adresse,:cp,:capacite,:photo)");
			}



			$enregistrement->bindParam(":description", $description, PDO::PARAM_STR);
			$enregistrement->bindParam(":titre", $titre, PDO::PARAM_STR);
			$enregistrement->bindParam(":categorie", $categorie, PDO::PARAM_STR);
			$enregistrement->bindParam(":pays", $pays, PDO::PARAM_STR);
			$enregistrement->bindParam(":ville", $ville, PDO::PARAM_STR);
			$enregistrement->bindParam(":adresse", $adresse, PDO::PARAM_STR);
			$enregistrement->bindParam(":cp", $cp, PDO::PARAM_STR);
			$enregistrement->bindParam(":capacite", $capacite, PDO::PARAM_STR);
			$enregistrement->bindParam(":photo", $photo_bdd, PDO::PARAM_STR);
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
if(isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_salle'])) {

	$infos_salle = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
	$infos_salle->bindparam(":id_salle", $_GET['id_salle'], PDO::PARAM_STR);
	$infos_salle->execute();

	if($infos_salle->rowCount() > 0) {
		$salle_actuel = $infos_salle->fetch(PDO::FETCH_ASSOC);

		$id_salle = $salle_actuel['id_salle'];
		$description = $salle_actuel['description'];
		$titre = $salle_actuel['titre'];
		$categorie = $salle_actuel['categorie'];
		$pays = $salle_actuel['pays'];
		$ville = $salle_actuel['ville'];
		$adresse = $salle_actuel['adresse'];
		$photo_actuelle = $salle_actuel['photo'];
		$cp = $salle_actuel['cp'];
		$capacite = $salle_actuel['capacite'];

	}
}


include '../inc/head.inc.php';
include '../inc/nav.inc.php';

 //echo '<pre>'; var_dump($_POST); echo '</pre>';
 //echo '<pre>'; var_dump($_SERVER); echo '</pre>';
 //echo '<pre>'; var_dump($_FILES); echo '</pre>';

?>


<div class="starter-template text-center mt-5">
		<h1><i  style="color: #4c6ef5;"></i> Gestion des salles <i  style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?= $msg ?></p>

		<p class="text-center">
			<a href="?action=ajouter" class="btn btn-outline-danger">Ajout salle</a>
			<a href="?action=affichage" class="btn btn-outline-primary">Affichage salle</a>
		</p>

	</div>

	<div class="row">
		<div class="col-12">





<?php
//***************************
		// AFFICHAGE DES ARTICLES
		//***************************

		if(isset($_GET['action']) && $_GET['action'] == 'affichage'):
			// on récupère les articles en bdd
			$liste_salle = $pdo->query("SELECT * FROM salle");?>


			<p>Nombre de salle : <b> <?= $liste_salle->rowCount() ?></b></p>
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr>
			<th>Id salle</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Photo</th>
			<th>Pays</th>
			<th>Ville</th>
			<th>Adresse</th>
			<th>Cp</th>
			<th>Capacite</th>
			<th>Categorie</th>
			<th>Modif</th>
			<th>Suppr</th>
			</tr>

			<?php while($salle = $liste_salle->fetch(PDO::FETCH_ASSOC)):?>
				<tr>
				<td><?= $salle['id_salle'] ?></td>
				<td><?= $salle['titre']?></td>
				<td><?= substr($salle['description'], 0, 14)?> ...</td>
				<td><img src="<?= URL . 'photo/' . $salle['photo']?>" class="img-thumbnail" width="140"></td>
				<td><?= $salle['pays']?></td>	
				
				<td><?= $salle['ville'] ?></td>
				<td><?= $salle['adresse'] ?></td>
				<td><?= $salle['cp'] ?></td>
				<td><?= $salle['capacite'] ?></td>
				<td><?= $salle['categorie'] ?></td>

<td><a href="?action=modifier&id_salle=<?= $salle['id_salle']?>" class="btn btn-warning"><i class="fas fa-edit"></i></a></td>

<td><a href="?action=supprimer&id_salle=<?= $salle['id_salle']?>" class="btn btn-danger" onclick="return(confirm('Etes-vous sûr ?'))"><i class="fas fa-trash-alt"></i></a></td>

				</tr>
			<?php endwhile ?>
			</table>
		</div>

			<?php endif

	//***************************
	// FIN AFFICHAGE DES ARTICLES
	//***************************
	?>
	</div>
	<div class="col-12">


		<?php
	//***************************
	// FORMULAIRE D'AJOUT ARTICLE
	//***************************
	// on affiche le form si l'utilisateur a cliqué sur le bouton "Ajout article"
		if(isset($_GET['action']) && ($_GET['action'] == 'ajouter' || $_GET['action'] == 'modifier')) {
		?>

		<?php
	// récupération de la photo de la salle en cas de modification. Pour la consever si l'utilisateur n'en charge pas une nouvelle
	if(!empty($photo_actuelle)) {?>
		<div class="form-group text-center">
		<label>Photo actuelle</label><hr>
		<img src="<?= URL . 'photo/' . $photo_actuelle ?>" class="w-50 img-thumbnail" alt="image de la salle">
		<input type="hidden" name="photo_actuelle" value="<?= $photo_actuelle ?>">
		</div>
	<?php } ?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="photo_actuelle">
<div class="form-group">
	<label for="photo">Photo</label>
	<input type="file" name="photo" id="photo" class="form-control" value="<?php echo $photo_bdd; ?>">
</div>

<hr>

<div class="form-group">
    <label for="exampleFormControlTextarea1">Description</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"  name="description"><?php echo $description; ?></textarea>
  </div>

<hr>

<div class="form-group">
    <label for="exampleInputEmail1">Titre</label>
    <input type="titre" class="form-control" id="titre" aria-describedby="titre" value="<?php echo $titre; ?>" name="titre">
     </div>
<hr>

<div class="form-group">
    <label for="exampleFormControlSelect1">Capacité</label>
    <select class="form-control" id="exampleFormControlSelect1" name="capacite">
	<option <?php if($capacite == '2') { echo 'selected'; } ?>>2</option>
      <option <?php if($capacite == '5') { echo 'selected'; } ?>>5</option>
      <option <?php if($capacite == '10') { echo 'selected'; } ?>>10</option>
      <option <?php if($capacite == '15') { echo 'selected'; } ?>>15</option>
      <option <?php if($capacite == '20') { echo 'selected'; } ?>>20</option>
      <option <?php if($capacite == '25') { echo 'selected'; } ?>>25</option>
    </select>
  </div>
  <hr>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Catégorie</label>
    <select class="form-control" id="exampleFormControlSelect1" name="categorie">
      <option <?php if($categorie == 'reunion') { echo 'selected'; } ?>>Réunion</option>
      <option <?php if($categorie == 'bureau') { echo 'selected'; } ?>>Bureau</option>
      <option <?php if($categorie == 'formation') { echo 'selected'; } ?>>Formation</option>
    </select>
  </div>
  <hr>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Pays</label>
    <select class="form-control" id="exampleFormControlSelect1" name="pays" >
      <option>France</option>
    </select>
  </div>
  <hr>
<div class="form-group">
  <label for="exampleFormControlSelect1">Ville</label>
  <select class="form-control" id="exampleFormControlSelect1" name="ville">
	<option <?php if($ville == 'paris') { echo 'selected'; } ?>>Paris</option>
	<option <?php if($ville == 'lyon') { echo 'selected'; } ?>>Lyon</option>
	<option <?php if($ville == 'marseille') { echo 'selected'; } ?>>Marseille</option>
  </select>
</div>
<hr>
<div class="form-group">
    <label for="exampleFormControlTextarea1">Adresse</label>
    <input class="form-control" id="exampleFormControlTextarea1" rows="3" name="adresse" value="<?php echo $adresse; ?>">
  </div>

 <hr>

 <div class="form-group">
    <label for="exampleInputEmail1">Code postal</label>
    <input type="text" class="form-control" id="cp" aria-describedby="cp" name="cp" value="<?php echo $cp; ?>">
	 </div>
	 <hr>

<div class="form-group">
	<button type="submit" name="enregistrement" id="enregistrement" class="form-control btn btn-outline-dark"> Enregistrement </button>
</div>


		</div>
	</div>
</form>

			<?php
			// fin du if(isset($_GET['action']) && $_GET['action'] == 'ajouter')
		}?>


		</div>
	</div>


<!-- /.row -->
<?php
include '../inc/footer.inc.php';