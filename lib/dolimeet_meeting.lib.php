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
 * \file    lib/dolimeet_meeting.lib.php
 * \ingroup dolimeet
 * \brief   Library files with common functions for Envelope
 */

/**
 * Prepare array of tabs for Envelope
 *
 * @param	Envelope	$object		Envelope
 * @return 	array					Array of tabs
 */

function remove_index($model) {
	if (preg_match('/index.php/',$model)) {
		return '';
	} else {
		return $model;
	}
}

function meetingPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("dolimeet@dolimeet");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	//Linked objects selection
	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_attendants.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Attendants");
	$head[$h][2] = 'attendants';
	$h++;

//	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_signature.php", 1) . '?id=' . $object->id;
//	$head[$h][1] = '<i class="fas fa-file-signature"></i> ' . $langs->trans("Sign");
//	$head[$h][2] = 'meetingSign';
//	$h++;

	if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
		$nbNote = 0;
		if (!empty($object->note_private)) {
			$nbNote++;
		}
		if (!empty($object->note_public)) {
			$nbNote++;
		}
		$head[$h][0] = dol_buildpath('/dolimeet/view/meeting/meeting_note.php', 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) {
			$head[$h][1] .= (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER) ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
		}
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->dolimeet->dir_output."/meeting/".dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
	$nbLinks = Link::count($db, $object->element, $object->id);
	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_document.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles + $nbLinks) > 0) {
		$head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
	}
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_agenda.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'agenda';
	$h++;

//	//Contact selection is unused
//	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_contact.php", 1).'?id='.$object->id;
//	$head[$h][1] = $langs->trans("ContactsAddresses");
//	$head[$h][2] = 'meetingContact';
//	$h++;

//	//Sending archive selection
//	$head[$h][0] = dol_buildpath("/dolimeet/view/meeting/meeting_sending.php", 1).'?id='.$object->id;
//	$head[$h][1] = $langs->trans("Sending");
//	$head[$h][2] = 'sending';
//	$h++;


	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@dolimeet:/dolimeet/view/meeting/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@dolimeet:/dolimeet/view/meeting/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'meeting@dolimeet');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'meeting@dolimeet', 'remove');

	return $head;
}
