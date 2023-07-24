<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
// vim: set ai ts=4 sw=4 ft=php:
function announcement_destinations() {
	// return an associative array with destination and description
	$extens = [];
	foreach (announcement_list() as $row) {
		$extens[] = ['destination' => 'app-announcement-'.$row['announcement_id'].',s,1', 'description' => $row[1]];
	}
	return $extens;
}

function announcement_getdest($exten) {
	return ['app-announcement-'.$exten.',s,1'];
}

function announcement_getdestinfo($dest) {
	global $active_modules;

	if (str_starts_with(trim((string) $dest), 'app-announcement-')) {
		$exten = explode(',',(string) $dest);
		$exten = substr($exten[0],17);

		$thisexten = announcement_get($exten);
		if (empty($thisexten)) {
			return [];
		} else {
			$type = $active_modules['announcement']['type'] ?? 'setup';
			return ['description' => sprintf(_("Announcement: %s"),$thisexten['description']), 'edit_url' => 'config.php?display=announcement&view=form&type='.$type.'&extdisplay='.urlencode($exten)];
		}
	} else {
		return false;
	}
}

function announcement_recordings_usage($recording_id) {
	$usage_arr = [];
 global $active_modules;

	$results = sql("SELECT announcement_id, description FROM announcement WHERE recording_id = '$recording_id'","getAll",DB_FETCHMODE_ASSOC);
	if (empty($results)) {
		return [];
	} else {
		$type = $active_modules['announcement']['type'] ?? 'setup';
		foreach ($results as $result) {
			$usage_arr[] = ['url_query' => 'config.php?display=announcement&type='.$type.'&extdisplay='.urlencode((string) $result['announcement_id']), 'description' => sprintf(_("Announcement: %s"),$result['description'])];
		}
		return $usage_arr;
	}
}

function announcement_get_config($engine) {
	global $ext;
	switch ($engine) {
		case 'asterisk':
			foreach (announcement_list() as $row) {
				$recording = recordings_get_file($row['recording_id']);
				if (! $row['noanswer']) {
					$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_gotoif('$["${CHANNEL(state)}" = "Up"]','begin'));
					$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_answer(''));
					$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_wait('1'));
				} else {
					$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_progress());
				}
				$ext->add('app-announcement-'.$row['announcement_id'], 's', 'begin', new ext_noop('Playing announcement '.$row['description']));
				if ($row['allow_skip'] || $row['repeat_msg']) {
					// allow skip
					if ($row['repeat_msg']) {
						$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_responsetimeout(1));
					}
					$ext->add('app-announcement-'.$row['announcement_id'], 's', 'play', new ext_background($recording.',nm'));
					if ($row['repeat_msg']) {
						$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_waitexten(''));
					}
					if ($row['allow_skip']) {
						$skip = "_[0-9*#]";
						$ext->add('app-announcement-'.$row['announcement_id'], $skip, '', new ext_noop('User skipped announcement'));
						if ($row['return_ivr']) {
							$ext->add('app-announcement-'.$row['announcement_id'], $skip, '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row['post_dest'].':${IVR_CONTEXT},return,1'));
						} else {
							$ext->add('app-announcement-'.$row['announcement_id'], $skip, '', new ext_goto($row['post_dest']));
						}
					}
					if ($row['repeat_msg']) {
						$ext->add('app-announcement-'.$row['announcement_id'], $row['repeat_msg'], '', new ext_goto('s,play'));
					}
				} else {
					$ext->add('app-announcement-'.$row['announcement_id'], 's', '', new ext_playback($recording.',noanswer'));
				}

				// if repeat_msg enabled then set exten to t to allow for the key to be pressed, otherwise play message and go
				$exten = $row['repeat_msg'] ? 't':'s';
				if ($row['return_ivr']) {
					$ext->add('app-announcement-'.$row['announcement_id'], $exten, '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row['post_dest'].':${IVR_CONTEXT},return,1'));
					if ($row['allow_skip'] || $row['repeat_msg'])
						$ext->add('app-announcement-'.$row['announcement_id'], 'i', '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row['post_dest'].':${IVR_CONTEXT},return,1'));
				} else {
					$ext->add('app-announcement-'.$row['announcement_id'], $exten, '', new ext_goto($row['post_dest']));
					if ($row['allow_skip'] || $row['repeat_msg'])
						$ext->add('app-announcement-'.$row['announcement_id'], 'i', '', new ext_goto($row['post_dest']));
				}

			}
		break;
	}
}

function announcement_list() {
	$results = \FreePBX::Announcement()->getAnnouncements();

	$results = is_array($results) ? $results : [];
	// Make array backward compatible.
	$count = 0;
	foreach($results as $item) {
		$results[$count][0] = $item['announcement_id'];
		$results[$count][1] = $item['description'];
		$results[$count][2] = $item['recording_id'];
		$results[$count][3] = $item['allow_skip'];
		$results[$count][4] = $item['post_dest'];
		$results[$count][5] = $item['return_ivr'];
		$results[$count][6] = $item['noanswer'];
		$results[$count][7] = $item['repeat_msg'];
		$count++;
	}
	return $results;
}

function announcement_get($announcement_id) {
	$row = \FreePBX::Announcement()->getAnnouncementByID($announcement_id);
	$i = 0;
	if(!empty($row) && is_array($row)) {
		foreach ($row as $item) {
			$row[$i] = $item;
			$i++;
		}
		return $row;
	} else {
		return [];
	}
}

function announcement_add($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) {
	FreePBX::Modules()->deprecatedFunction();
	return FreePBX::Announcement()->addAnnoucement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
}

function announcement_delete($announcement_id) {
	FreePBX::Modules()->deprecatedFunction();
	return FreePBX::Announcement()->deleteAnnoucement($announcement_id);
}

function announcement_edit($announcement_id, $description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) {
	FreePBX::Modules()->deprecatedFunction();
	return FreePBX::Announcement()->editAnnoucement($announcement_id, $description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
}

function announcement_check_destinations($dest=true) {
	global $active_modules;

	$destlist = [];
	if (is_array($dest) && empty($dest)) {
		return $destlist;
	}
	$sql = "SELECT announcement_id, post_dest, description FROM announcement ";
	if ($dest !== true) {
		$sql .= "WHERE post_dest in ('".implode("','",$dest)."')";
	}
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	$type = $active_modules['announcement']['type'] ?? 'setup';

	foreach ($results as $result) {
		$thisdest = $result['post_dest'];
		$thisid   = $result['announcement_id'];
		$destlist[] = ['dest' => $thisdest, 'description' => sprintf(_("Announcement: %s"),$result['description']), 'edit_url' => 'config.php?display=announcement&view=form&type='.$type.'&extdisplay='.urlencode((string) $thisid)];
	}
	return $destlist;
}

function announcement_change_destination($old_dest, $new_dest) {
	$sql = 'UPDATE announcement SET post_dest = "' . $new_dest . '" WHERE post_dest= "' . $old_dest . '"';
	sql($sql, "query");
}
