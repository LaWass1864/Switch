<?php 
include '../inc/init.inc.php';
include '../inc/fonction.inc.php';

// Si le utilisateur est un admin, il peut acceder a cette page
if(!user_is_admin()) {
	header('location:' . URL . 'connexion.php');
	exit(); // bloque l'exécution du code 
	}

// Je recupre les infos depuis la BDD



$recup_commande = $pdo -> query('SELECT c.id_commande, c.id_membre, c.id_produit, p.prix, DATE_FORMAT(c.date_enregistrement, "%d/%m/%Y %H:%m") as date_enregistrement  FROM produit p, commande c WHERE c.id_produit = p.id_produit');
$commande = $recup_commande -> fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$resultat = $pdo -> prepare('SELECT c.id_commande, c.id_membre, c.id_produit, p.prix, DATE_FORMAT(c.date_enregistrement, "%d/%m/%Y %H:%m") as date_enregistrement  FROM produit p, commande c WHERE c.id_produit = p.id_produit AND c.id_commande = :id');
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();

	if ($resultat -> rowCount() > 0) {
		$commande_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
	} 
}

$id_commande = (isset($commande_actuel)) ? $commande_actuel['id_commande'] : '';
$prix = (isset($commande_actuel)) ? $commande_actuel['prix'] : '';
$date_enregistrement = (isset($commande_actuel)) ? $commande_actuel['date_enregistrement'] : '';

//*********************************************************************
//*********************************************************************
// \FIN SUPPRESSION D'UNE COMMANDE
//*********************************************************************
//*********************************************************************
$id_membre ='';
$id_produit = ''; // pour la modification	
$id_commande = '';
$prix = '';
$date_enregistrement = '';
$actions = '';


// je crée une 

include '../inc/head.inc.php';
include '../inc/nav.inc.php';
?>


<div class="starter-template text-center mt-5">
<h1><i style="color: #4c6ef5;"></i> Gestion des commandes <i style="color: #4c6ef5;"></i></h1>
<p class="lead"><?php echo $msg; ?></p>

</div>		


<div class="row">
<div class="col-12">

<?php
//***************************
// AFFICHAGE DES ARTICLES
//***************************

	// on récupère les articles en bdd
	$liste_commande = $pdo->query("SELECT * FROM produit p, commande c WHERE c.id_produit = p.id_produit");			
	$liste_commande->rowCount() . '</b></p>';
	
	echo '<div class="table-responsive">';
	echo '<table class="table table-bordered">';
	echo '<tr>';
	echo '<th>Id commande</th>';
	echo '<th>Id membre</th>';
	echo '<th>Id produit</th>';
	echo '<th>Prix</th>';
	echo '<th>Date enregistrement</th>';
	echo '<th>Actions</th>';			
				
	echo '<th>Modif</th>';
	echo '<th>Suppr</th>';
	echo '</tr>';
	
	while($commande = $liste_commande->fetch(PDO::FETCH_ASSOC)) {
		echo '<tr>';
		echo '<td>' . $commande['id_commande'] . '</td>';
		echo '<td>' . $commande['id_membre'] . '</td>';
		echo '<td>' . $commande['id_produit'] . '</td>';
		echo '<td>' . $commande['prix'] . '</td>';			
		echo '<td>' . $commande['date_enregistrement'] . '</td>';
		echo '<td>' . $commande['etat'] . '</td>';		
								
						
		
echo '<td><a href="?action=modifier&id_commande=' . $commande['id_commande'] . '" class="btn btn-warning"><i class="fas fa-edit"></i></a></td>';

echo '<td><a href="?action=supprimer&id_commande=' . $commande['id_commande'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))">
<i class="fas fa-trash-alt"></i></a></td>';
		
		echo '</tr>';
	}
	
	echo '</table>';
	echo '</div>';
 
?>	
</div>
</div>
<?php 
include '../inc/footer.inc.php';