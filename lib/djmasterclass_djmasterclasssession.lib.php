<?php
/* Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/djmasterclass_djmasterclasssession.lib.php
 * \ingroup djmasterclass
 * \brief   Library files with common functions for DjMasterclassSession
 */

/**
 * Prepare array of tabs for DjMasterclassSession
 *
 * @param	DjMasterclassSession	$object		DjMasterclassSession
 * @return 	array					Array of tabs
 */
function djmasterclasssessionPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("djmasterclass@djmasterclass");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/djmasterclass/djmasterclasssession_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	if (isset($object->fields['note_public']) || isset($object->fields['note_private']))
	{
		$nbNote = 0;
		if (!empty($object->note_private)) $nbNote++;
		if (!empty($object->note_public)) $nbNote++;
		$head[$h][0] = dol_buildpath('/djmasterclass/djmasterclasssession_note.php', 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1] .= (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER) ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->djmasterclass->dir_output."/djmasterclasssession/".dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
	$nbLinks = Link::count($db, $object->element, $object->id);
	$head[$h][0] = dol_buildpath("/djmasterclass/djmasterclasssession_document.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles + $nbLinks) > 0) $head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/djmasterclass/djmasterclasssession_agenda.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'agenda';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@djmasterclass:/djmasterclass/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@djmasterclass:/djmasterclass/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'djmasterclasssession@djmasterclass');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'djmasterclasssession@djmasterclass', 'remove');

	return $head;
}

function send_email($session, $stagiaire, $label='MASTERCLASS_INSCRIPTION') {

	global $db, $user, $langs, $conf;

	if(empty($conf->global->MAIN_INFO_SOCIETE_MAIL) || empty($stagiaire->email)) return 0;

	require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';

	$formmail = new FormMail($db);
	$tpl = $formmail->getEMailTemplate($db, $type_template, $user, $outputlangs, $id = 0, $active = 1, $label);

	if(empty($tpl->id)) return 0;

	$subject = $tpl->topic;
	$sendto = $stagiaire->lastname.' '.$stagiaire->firstname." <".$stagiaire->email.">";
	$from = $conf->global->MAIN_INFO_SOCIETE_NOM." <".$conf->global->MAIN_INFO_SOCIETE_MAIL.">";
	$msgishtml = 1;
	$trackid = 'use'.$user->id;

	$arr_file = array();
	$arr_mime = array();
	$arr_name = array();

	$message = strtr($tpl->content, array('__PARCICIPANT__'=>$stagiaire->firstname, '__DESCRIPTION_MASTERCLASS__'=>'session masterclass "'.$session->label.'"'
			, '__CONFIRMATION_LINK__'=>$_SERVER['SERVER_NAME'].'?token_reservation='.$stagiaire->token_reservation));

	$mailfile = new CMailFile($subject, $sendto, $from, $message, $arr_file, $arr_mime, $arr_name, '', '', 0, $msgishtml, $user->email, '', $trackid);

	$result = $mailfile->sendfile();

}
