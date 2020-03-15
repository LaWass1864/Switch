<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?php echo URL; ?>index.php">Switch</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto"                 
          
          <li class="nav-item">
            <a class="nav-link" href="../quisommesnous.php">Qui sommes-nous ?</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link" href="../contactez-nous.php">Contactez-nous...</a>
          </li>
		  


          <!-- L'utilisateur est connecté -->
		  <?php if(!user_is_connect()) { ?>			
			
		  		  
		  
		  <?php } else { ?>
		  
		  <li class="nav-item">
			<a class="nav-link" href="<?php echo URL; ?>profil.php">Profil</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="<?php echo URL; ?>connexion.php?action=deconnexion">Déconnexion</a>
		  </li>
		  <li class="nav-item">
            <a class="nav-link" href="<?php echo URL; ?>fiche_article.php">Fiche produit</a>
          </li>
		  
		  <?php } ?>



		  
		  <!-- L'utilisateur est l'admin -->
		  
		  <?php if(user_is_admin()) : ?>

		  <li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Administration</a>
			<div class="dropdown-menu" aria-labelledby="dropdown01">
			  <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_salles.php">Gestion des salles</a>
			  <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_membres.php">Gestion des membres</a>
			  <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_produits.php">Gestion des produits</a>
			  <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_avis.php">Gestion des avis</a>
			  <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_commandes.php">Gestion des commandes</a>
			</div>
		  </li>
		  
		  <?php endif; ?>
		  <li class="nav-item">
			<a class="nav-link" href="<?php echo URL; ?>connexion.php?action=deconnexion"> Connexion</a>
		  </li>

		  <li class="nav-item">
			<a class="nav-link" href="<?php echo URL; ?>inscription.php?action=inscription"> Inscription</a>
		  </li>

		 
		
      
        </ul>
        
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">