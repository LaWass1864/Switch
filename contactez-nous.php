<?php 
include 'inc/init.inc.php';
include 'inc/fonction.inc.php';

	// code .




include 'inc/head.inc.php';
include 'inc/nav.inc.php';
?>


<div class="starter-template text-center mt-5">
		<h1><i style="color: #4c6ef5;"></i> Contactez-nous <i style="color: #4c6ef5;"></i></h1>
		<p class="lead"><?php echo $msg; ?></p>
	</div>

	<div class="row">
		<div class="col-12">

<form>
  <div class="form-group">
    <label for="exampleFormControlInput1">Email</label>
    <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
  </div>
  
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Votre message</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Votre message"></textarea>
  </div>
</form>
		
		</div>
	</div>
 

<!-- /.row -->
<?php 
include 'inc/footer.inc.php';