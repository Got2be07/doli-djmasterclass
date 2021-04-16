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
	require_once __DIR__ . '/../lib/djmasterclass_djmasterclasssession.lib.php';

	$nom = GETPOST('nom', 'alpha');
	$prenom = GETPOST('prenom', 'alpha');
	$email = GETPOST('email', 'alpha');
	$is_valid_email = !empty(filter_var($email, FILTER_VALIDATE_EMAIL));
	$phone = GETPOST('phone', 'alpha');
	$id_masterclass = GETPOST('id_masterclass', 'alpha');
	$token_reservation = GETPOST('token_reservation', 'alpha');
	$action = GETPOST('action', 'alpha');

	/*
		Actions
	*/

	if($action === 'add_reservation' && !empty($id_masterclass) && !empty($prenom) && !empty($nom) && $is_valid_email) {

		$sess = new DjMasterclassSession($db);
		if($sess->fetch($id_masterclass) > 0) {

			$obj_reservation = new djmasterclassstagiaire($db);
			$TReservations = $obj_reservation->fetchAll('', '', 0, 0, array('email' => $email, 'fk_djmasterclasssession' => $id_masterclass));

			if (empty($TReservations)) { // N'est pas déjà enregistré pour cette session
				if($sess->getNbPlacesRestantes() > 0) {
					$reservation = new djmasterclassstagiaire($db);
					$reservation->fk_djmasterclasssession = $id_masterclass;
					$reservation->lastname = $nom;
					$reservation->firstname = $prenom;
					$reservation->email = $email;
					$reservation->phone = $phone;
					$reservation->token_reservation = RandomString();
					$reservation->status = 0;

					$reservation->create($user);

					//$TMsg = array('msg'=>"Inscription effectuée avec succès, pensez à la confirmer grâce au lien disponible sur le mail que vous avez reçu", 'style'=>'success');
					$TMsg = array('msg'=>"Parfait ! pensez à vérifier vos emails...", 'style'=>'success');

					send_email($sess, $reservation, 'MASTERCLASS_INSCRIPTION');

				} else {
					$TMsg = array('msg'=>'Il n\'y a plus de place pour cette session', 'style'=>'warn');
				}
			} else $TMsg = array('msg'=>'Vous êtes déjà inscrit(e) à cette session', 'style'=>'info');
		}

	} elseif(!empty($token_reservation)) {
		$obj_reservation = new djmasterclassstagiaire($db);
		$TReservations = $obj_reservation->fetchAll('', '', 0, 0, array('token_reservation'=>$token_reservation));
		if(!empty($TReservations)) {
			$reservation = $TReservations[key($TReservations)];
			if(empty($reservation->status)) {
				$reservation->status = 1;
				if($reservation->update($user) > 0) {
			                $sess = new DjMasterclassSession($db);
			                $sess->fetch($reservation->fk_djmasterclasssession);
					send_email($sess, $reservation, 'MASTERCLASS_CONFIRMATION');
				}
			}
			$TMsg = array('msg'=>'Votre réservation est confirmée !', 'style'=>'success');
		} else {
			$TMsg = array('msg'=>'Lien invalide, veuillez vous réinscrire', 'style'=>'warn');
		}
	}

	/*
		View
	*/
	$obj_masterclass = new djmasterclasssession($db);
	$TAvailableSessions = $obj_masterclass->getAvailableSessions();
//	$TAvailableSessions = $obj_masterclass->fetchAll('', '', 0, 0, array('status'=>1, 'customsql'=>' date_session > "'.date('Y-m-d').'"'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title><?php print $conf->global->MAIN_INFO_SOCIETE_NOM; ?></title>

	<!-- JQuery -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="https://djsfrenchcandy.com/gestion/htdocs/custom/djmasterclass/lib/colorlib-booking-1/css/bootstrap.min.css" />

        <!-- Notify -->
        <script type="text/javascript" src="https://djsfrenchcandy.com/gestion/htdocs/custom/djmasterclass/js/notify.js"></script>

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="https://djsfrenchcandy.com/gestion/htdocs/custom/djmasterclass/lib/colorlib-booking-1/css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

<?php
	if(!empty($TMsg)) print '
	        <script type="text/javascript">
	                $(document).ready(function(){
				$.notify("'.$TMsg['msg'].'", {position:"top right", autoHideDelay:"20000", className:"'.$TMsg['style'].'"});
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
					<?php if(isMobile()) print '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'; ?>
						<div class="booking-cta<?php if(isMobile()) print '-mob'; ?>">
							<h1><?php print $conf->global->MAIN_INFO_SOCIETE_NOM; ?></h1>
							<p><?php empty($conf->global->MASTERCLASS_FORMULAIRE_INSCRIPTION_DESCRIPTION) 
									? print 'Réservez sans plus attendre votre première session de formation DJ !'
									: print $conf->global->MASTERCLASS_FORMULAIRE_INSCRIPTION_DESCRIPTION ?>
							</p>
						</div>
					</div>
					<div class="col-md-4 col-md-pull-7">
						<div class="booking-form">
							<?php

									print '<form name="reservation_masterclass_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">
											<input type="hidden" name="action" value="add_reservation" />
											<div class="form-group">';


											if(empty($TAvailableSessions)) {
												print '<span style="color:red;" class="form-label">Aucune session disponible actuellement';
											} else {
												print '<span class="form-label">Session masterclass</span>';
												print '<select name="id_masterclass" class="form-control" id="id_masterclass">';
												foreach ($TAvailableSessions as $key => $value) {
													print '<option value="'.$value->id.'"';
													if($value->id==$id_masterclass) print ' selected="selected"';
													print '>'.$value->label.'&nbsp;('.$value->amount.'€)</option>';
												}
												print '</select>';

											}

											print '<span class="select-arrow"></span>
											</div>
											<div class="form-group">
												<div class="form-group">
													<span class="form-label">Nom&nbsp;';
													if(empty($nom) && !empty($action)) print '<span style="color:red;">* champ obligatoire</span>';
											print '</span>
													<input value="'.$nom.'" name="nom" class="form-control" type="text" placeholder="Renseignez votre nom">
												<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Prénom&nbsp;';
													if(empty($prenom) && !empty($action)) print '<span style="color:red;">* champ obligatoire</span>';
											print '</span>
													<input name="prenom" value="'.$prenom.'" class="form-control" type="text" placeholder="Renseignez votre prénom">
													<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Adresse email&nbsp;';
													if((empty($email) || !$is_valid_email) && !empty($action)) print '<span style="color:red;">* email incorrect</span>';
											print '</span>
													<input  value="'.$email.'" name="email" class="form-control" type="text" placeholder="Renseignez votre adresse email">
													<span class="select-arrow"></span>
												</div>
												<div class="form-group">
													<span class="form-label">Numéro de téléphone</span>
													<input value="'.$phone.'" name="phone" class="form-control" type="text" placeholder="Renseignez votre numéro de téléphone">
													<span class="select-arrow"></span>
												</div>
											</div>
											<br />
											<div class="form-btn">
												<button class="submit-btn"';
											if(empty($TAvailableSessions)) print ' disabled="disabled" title="Aucune session disponible actuellement"';
											print '>Réserver session</button>
											</div>
											</form>';

											?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
