<?php
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

// récupération des catégories en BDD
$liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM salle ORDER BY categorie");

$liste_ville = $pdo->query("SELECT DISTINCT ville FROM salle ORDER BY cp");

$prix = $pdo->query("SELECT DISTINCT prix FROM produit ORDER BY prix DESC");

// Récupération des articles en BDD
if(isset($_GET['categorie'])) {
    $choix_salle = $_GET['categorie'];
    $liste_salle = $pdo->prepare("SELECT * FROM salle AS s INNER JOIN produit AS p ON p.id_salle = s.id_salle WHERE categorie = :categorie ORDER BY titre");
    $liste_salle->bindParam(':categorie', $choix_categorie, PDO::PARAM_STR);
    $liste_salle->execute();

} elseif(isset($_GET['ville'])) {
    $choix_ville = $_GET['ville'];
    $liste_salle = $pdo->prepare("SELECT * FROM salle AS s LEFT JOIN produit AS p ON p.id_salle = s.id_salle WHERE s.ville = :ville ORDER BY s.titre");
    $liste_salle->bindParam(':ville', $choix_ville, PDO::PARAM_STR);
    $liste_salle->execute();

} elseif(isset($_GET['prix'])) {
    $prix = $_GET['prix'];
    $prix = $pdo->prepare("SELECT * FROM produit WHERE prix = :prix ORDER BY prix");
    $prix->bindParam(':prix', $prix, PDO::PARAM_STR);
    $prix->execute();
}
else {
    $liste_salle = $pdo->query("SELECT * FROM produit AS p RIGHT JOIN salle AS s ON p.id_salle = s.id_salle ORDER BY s.titre");
}





include 'inc/head.inc.php';
include 'inc/nav.inc.php';
?>

    <div class="starter-template text-center mt-5">
        <h1><i  style="color: #4c6ef5;"></i> Accueil <i  style="color: #4c6ef5;"></i></h1>
        <p class="lead"><?php echo $msg; ?></p>
    </div>

    <div class="row">
        <div class="col-3">
            <!-- Récupérer la liste des catégories salle en BDD pour les afficher dans des liens a href="" dans une liste ul li -->
            <?php
            // partie Categorie

            echo '<ul class="list-group">
                        <li class="list-group-item active">Catégories</li>';
                        
            echo '<li class="list-group-item"><a href="' . URL . '">Tous les produits</a></li>';

            while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
                // echo '<pre>'; var_dump($categorie); echo '</pre><hr>';
                echo '<li class="list-group-item"><a href="' . URL . '?categorie=' . $categorie['categorie'] . '">' . $categorie['categorie'] . '</a></li>';
            }


            
            echo '</ul>';
          
            echo '<hr>';

            // Partie ville

            echo '<ul class="list-group">
                        <li class="list-group-item active">Villes</li>';
                        
                        while($ville= $liste_ville->fetch(PDO::FETCH_ASSOC)) {
                            // echo '<pre>'; var_dump($categorie); echo '</pre><hr>';
                            echo '<li class="list-group-item"><a href="' . URL . '?ville=' . $ville['ville'] . '">' . $ville['ville'] . '</a></li>';
        
                        }	            

        
            echo '</ul>';

            echo '<hr>';

            // Partie Capacité
            echo'<li class="list-group-item active">Capacité</li>';

            

            echo '<select class="form-control" id="exampleFormControlSelect1">';
            echo  '<option>10</option>';
            echo  '<option>15</option>';
            echo  '<option>20</option>';
            echo  '<option>25</option>';
            echo  '<option>30</option>';
            echo   '</select>';
           /*while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
                // echo '<pre>'; var_dump($categorie); echo '</pre><hr>';
                echo '<li class="list-group-item"><a href="?categorie=' . $categorie['categorie'] . '">' . $categorie['categorie'] . '</a></li>';
            }*/


            echo '<hr>';

            // Partie prix

            echo '<div class="slidecontainer">';
            echo '<li class="list-group-item active">Prix</li>';
            echo '<p>Maximum 500€</p>';
            echo '<input type="range" min="1" max="1000" value="50" class="form-control-range" id="formControlRange">';
            echo '</div>';
            
           
            echo '<hr>';

            // Partie date picker (date arrivée + date depart)

            echo '<div class="form-group">';
            echo '<li class="list-group-item active">Date d\'arrivée</li>';
            echo '<div class="input-group date" id="datetimepicker1">';
            echo    '<input type="text" class="form-control" />';
            echo    '<span class="input-group-addon">';
            '<span class="glyphicon glyphicon-calendar"></span>';
            echo    '</span>';

            echo '</div>';

            echo '<hr>';
            // date de depart


            echo '<div class="form-group">';
            echo '<li class="list-group-item active">Date de départ</li>';

            echo    '<input type="text" class="form-control" />';
            echo    '<span class="input-group-addon">';
            '<span class="glyphicon glyphicon-calendar"></span>';
            echo    '</span>';
            echo '</div>';

            ?>

            <!-- //  Js pour le date picker -->


            <script type="text/javascript">
                $(function () {
                    $('#datetimepicker1').datetimepicker();
                });
            </script>
        </div>


    </div>
    <div class="col-9">
        <div class="row justify-content-around">
            <?php
                // var_dump($liste_salle->fetchAll(PDO::FETCH_ASSOC));
                
            // affichage des salles
            while($salle = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
               var_dump($salle);
                // echo '<pre>'; var_dump($salle); echo '</pre><hr>';
                echo '<div class="col-sm-3 text-center p-2">';

                echo '<h5>' . $salle['titre'] . '</h5>';

                echo '<img src="' . URL . 'photo/' . $salle['photo'] . '" alt="' . $salle['titre'] . '" class="img-thumbnail w-100">';

                // Afficher la catégorie, le prix.
                echo '<p>Catégorie : <b>' . $salle['categorie'] . '</b><br>';
                echo 'Prix : <b>' . $salle['prix'] . '€</b></p>';

                // bouton voir la fiche salle
                echo '<a href="fiche_article.php?id_article=' . $salle['id_produit'] . '" class="btn btn-primary w-100">Fiche salle</a><hr>';

                echo '</div>';
            }

            ?>
        </div>
    </div>
    </div>



<?php
include 'inc/footer.inc.php';