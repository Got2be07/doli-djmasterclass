<?php


	if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', '1'); // Disables token renewal
	if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', '1');
	if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', '1');
	if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');
	if (!defined('NOLOGIN'))        define('NOLOGIN', '1');

	require_once __DIR__ . '/../../../main.inc.php';
	require_once __DIR__ . '/../class/djmasterclasssession.class.php';
	require_once __DIR__ . '/../class/djmasterclassstagiaire.class.php';
	require_once __DIR__ . '/../lib/djmasterclass_djmasterclassstagiaire.lib.php';
	
	$nom = GETPOST('nom', 'alpha');
	$prenom = GETPOST('prenom', 'alpha');
	$email = GETPOST('email', 'alpha');
	$phone = GETPOST('phone', 'alpha');
	$id_masterclass = GETPOST('id_masterclass', 'alpha');
	$action = GETPOST('action', 'alpha');

	/*
		Actions
	*/

	if($action === 'add_reservation' && !empty($id_masterclass) && !empty($prenom) && !empty($nom) && !empty($email)) {

	        $obj_reservation = new djmasterclassstagiaire($db);
	        $TReservations = $obj_reservation->fetchAll('', '', 0, 0, array('email'=>$email, 'fk_djmasterclasssession'=>$id_masterclass));

		if(empty($TReservations)) { // N'est pas déjà enregistré pour cette session
			$reservation = new djmasterclassstagiaire($db);
			$reservation->fk_djmasterclasssession = $id_masterclass;
			$reservation->lastname = $nom;
			$reservation->firstname = $prenom;
			$reservation->email = $email;
			$reservation->phone = $phone;
			$reservation->token = RandomString();
			$reservation->status = 0;

			$reservation->create($user);

			$msg = '<h1>Inscription effectuée avec succès, pensez à la confirmer grâce au lien disponible sur le mail que vous avez reçu</h1>';
		} else $msg = 'Vous êtes déjà inscrit(e) à cette session';

	}

	/*
		View
	*/
	$obj_masterclass = new djmasterclasssession($db);
	$TAvailableSessions = $obj_masterclass->fetchAll('', '', 0, 0, array('status'=>1));

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Booking Form HTML Template</title>

	<!-- JQuery -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="../lib/colorlib-booking-1/css/bootstrap.min.css" />

        <!-- Notify -->
        <script type="text/javascript" src="../js/notify.js"></script>

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="../lib/colorlib-booking-1/css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

<?php
	if(!empty($msg)) print '
	        <script type="text/javascript">
	                $(document).ready(function(){
				$.notify("'.$msg.'", "info");
	                });
	        </script>';
?>

</head>

<body>
	<div id="booking" class="section">
		<div class="section-center">
			<div class="container">
				<div class="row">
					<div class="col-md-7 col-md-push-5">
						<div class="booking-cta">
							<h1>Drôme DJ Academy</h1>
							<p>Réservez sans plus attendre votre première session de formation DJ !
							</p>
						</div>
					</div>
					<div class="col-md-4 col-md-pull-7">
						<div class="booking-form">
							<?php

								if(!empty($TAvailableSessions)) {
									print '<form name="reservation_masterclass_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">
											<input type="hidden" name="action" value="add_reservation" />
											<div class="form-group">
									

											<span class="form-label">Session masterclass</span>
											<select name="id_masterclass" class="form-control" id="id_masterclass">';
										
											foreach ($TAvailableSessions as $key => $value) {
												print '<option value="'.$value->id.'">'.$value->label.'</option>';
											}

											print '</select>

											<span class="select-arrow"></span>
											</div>
											<div class="form-group">
												<div class="form-group">
													<span class="form-label">Nom</span>
													<input value="'.$nom.'" name="nom" class="form-control" type="text" placeholder="Renseignez votre nom">';
													if(empty($nom) && !empty($action)) print '<span style="color:red;">* Champ obligatoire</span>';
											print '<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Prénom</span>
													<input name="prenom" value="'.$prenom.'" class="form-control" type="text" placeholder="Renseignez votre prénom">';
													if(empty($prenom) && !empty($action)) print '<span style="color:red;">* Champ obligatoire</span>';
											print '<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Adresse email</span>
													<input  value="'.$email.'" name="email" class="form-control" type="text" placeholder="Renseignez votre adresse email">';
													if(empty($email) && !empty($action)) print '<span style="color:red;">* Champ obligatoire</span>';
											print '<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Numéro de téléphone</span>
													<input value="'.$phone.'" name="phone" class="form-control" type="text" placeholder="Renseignez votre numéro de téléphone">
													<span class="select-arrow"></span>
												</div>
											</div>
											<div class="form-btn">
												<button class="submit-btn">Réserver session</button>
											</div>
											</form>';
								} else {

									print '<span class="form-label">Aucun session disponible actuellement !</span>';

								}

											?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
