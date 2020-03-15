<?php 
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

// restriction d'accès, si l'utilisateur n'est pas connecté, on le renvoie sur connexion.php
if(!user_is_connect()) {
	header('location:connexion.php');
}




include 'inc/head.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; var_dump($_SESSION); echo '</pre>';
?>

	<div class="starter-template text-center mt-5">
		<h1><i style="color: #4c6ef5;"></i> Profil <i style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="row">
				<div class="col-6">
					<ul class="list-group">
						<li class="list-group-item active">Bonjour <b><?php echo ucfirst($_SESSION['membre']['pseudo']); ?></b></li>
						<li class="list-group-item">Pseudo : <b><?php echo ucfirst($_SESSION['membre']['pseudo']); ?></b></li>
						<li class="list-group-item">Nom : <b><?php echo ucfirst($_SESSION['membre']['nom']); ?></b></li>
						<li class="list-group-item">Prénom : <b><?php echo ucfirst($_SESSION['membre']['prenom']); ?></b></li>
						<li class="list-group-item">Email : <b><?php echo $_SESSION['membre']['email']; ?></b></li>
						<li class="list-group-item">Civilité :
				<?php 
				if($_SESSION['membre']['civilite'] == 'm'){
					echo 'homme';
				}
				elseif($_SESSION['membre']['civilite'] == 'f'){
					echo 'femme';
				}; 
				?>
			</li>
											
						<li class="list-group-item">Statut : <b>
						<?php 
							if($_SESSION['membre']['statut'] == 0) {
								echo 'membre';
							} elseif($_SESSION['membre']['statut'] == 1) {
								echo 'administrateur';
							}
						?>

						
						</b></li>
					</ul>
				</div>
				<div class="col-6">
					<img src="photo/profil.jpg" alt="image profil" class="img-thumbnail w-75">
				</div>
			</div>
		</div>
	</div>


<?php 
include 'inc/footer.inc.php';