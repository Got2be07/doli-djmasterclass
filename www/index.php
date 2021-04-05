<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Booking Form HTML Template</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="../lib/colorlib-booking-1/css/bootstrap.min.css" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="../lib/colorlib-booking-1/css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>


<?php

	require_once __DIR__ . '/../../../main.inc.php';
	require_once __DIR__ . '/../class/djmasterclasssession.class.php';
	
	$obj_masterclass = new djmasterclasssession($db);
	$TAvailableSessions = $obj_masterclass->fetchAll('', '', 0, 0, array('status'=>1));

?>

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
							<form>
								<div class="form-group">
									<!--<input class="form-control" type="text" placeholder="Enter a destination or hotel name">-->
										<div class="form-group">
											<span class="form-label">Session masterclass</span>
											<select class="form-control" id="id_masterclass">
												<?php
													foreach ($TAvailableSessions as $key => $value) {
														print '<option value="'.$value->id.'">'.$value->label.'</option>';
													}
												?>
											</select>
											<span class="select-arrow"></span>
										</div>
								</div>
								<div class="form-group">
									<div class="form-group">
										<span class="form-label">Nom</span>
										<input class="form-control" type="text" placeholder="Renseignez votre nom">
										<span class="select-arrow"></span>
									</div>
									<div class="form-group">
										<span class="form-label">Prénom</span>
										<input class="form-control" type="text" placeholder="Renseignez votre prénom">
										<span class="select-arrow"></span>
									</div>
									<div class="form-group">
										<span class="form-label">Adresse email</span>
										<input class="form-control" type="text" placeholder="Renseignez votre adresse email">
										<span class="select-arrow"></span>
									</div>
								</div>
								<div class="form-btn">
									<button class="submit-btn">Réserver session</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>