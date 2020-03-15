<?php 
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

if(!isset($_GET['id_produit'])) {
	


$infos_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
$infos_produit->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
$infos_produit->execute();

}

if($infos_produit->rowCount() < 1) {
	


$produit = $infos_produit->fetch(PDO::FETCH_ASSOC);

}

include 'inc/head.inc.php';
include 'inc/nav.inc.php';
?>

<?php

$infos_produit = $pdo->query("SELECT * FROM produit,salle");			
$infos_produit->rowCount() . '</b></p>';

?>

<div class="starter-template text-center mt-5">
		<h1>Front Office / Fiche produit</h1>
		<p class="lead"><?php echo $msg; ?></p>
        <div class="row">
		<div class="col-6">
			<ul class="list-group">
				<li class="list-group-item active"><b><?php echo $produit['id_salle']; ?></b></li>
				<li class="list-group-item">date de depart : <b><?php echo $produit['date_depart']; ?></b></li>
				<li class="list-group-item">date d'arrivée : <b><?php echo $produit['date_arrivee']; ?></b></li>
				<li class="list-group-item">Prix : <b><?php echo $produit['prix']; ?></b></li>
				<li class="list-group-item">Etat : <b><?php echo $produit['taille']; ?></b></li>
				
				
				<?php if($produit['stock'] > 0) { ?>
				
				<li class="list-group-item">Stock : <b><?php echo $produit['stock']; ?></b></li>
				
				<?php } else { ?>
				
				<li class="list-group-item"><span class="text-danger">Rupture de stock pour cette salle</span></li>
				
				<?php } ?>
				
				<li class="list-group-item">Prix : <b><?php echo $produit['prix']; ?></b>€</li>
				<li class="list-group-item">Description : <?php echo $produit['description']; ?></li>
			</ul>
			
		</div>
		<div class="col-6">
			<?php if($produit['stock'] > 0) { ?>
			<form method="post" action="panier.php">
				<input type="hidden" name="id_produit" value="<?php echo $produit['id_salle']; ?>">
				<div class="form-row">
					<div class="col">
						<select name="quantite" class="form-control">
						<?php
							for($i = 1; $i <= $produit['stock'] && $i <= 5; $i++) {
								echo '<option>' . $i . '</option>';
							}
						?>
						</select>
					</div>
					<div class="col">
						<button type="submit" class="btn btn-primary w-100" name="ajouter_au_panier">Ajouter au panier</button>
					</div>
				</div>
			</form>
			<hr>
			<?php } ?>
			
			<img src="<?php echo URL . '/photo/' . $produit['photo']; ?>" alt="<?php echo $produit['titre']; ?>" class="w-75 img-thumbnail">
		</div>
	</div>

</div>    

<!-- /.row -->
<?php 
include 'inc/footer.inc.php';