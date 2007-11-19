<?php

function announcement_destinations() {
	// return an associative array with destination and description
	$extens = array();
	foreach (announcement_list() as $row) {
		$extens[] = array('destination' => 'app-announcement-'.$row[0].',s,1', 'description' => $row[1]);
	}
	return $extens;
}

function announcement_get_config($engine) {
	global $ext;
	switch ($engine) {
		case 'asterisk':
			foreach (announcement_list() as $row) {
				if (! $row[6]) {
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_gotoif('$["${CDR(disposition)}" = "ANSWERED"]','begin'));
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_answer(''));
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_wait('1'));
				}
				$ext->add('app-announcement-'.$row[0], 's', 'begin', new ext_noop('Playing announcement '.$row[1]));
				if ($row[3] || $row[7]) {
					// allow skip
					if ($row[7]) {
						$ext->add('app-announcement-'.$row[0], 's', '', new ext_responsetimeout(3));
					}
					$ext->add('app-announcement-'.$row[0], 's', 'play', new ext_background($row[2].'|nm'));
					
					if ($row[3]) {
						$ext->add('app-announcement-'.$row[0], '_X', '', new ext_noop('User skipped announcement'));
						if ($row[5]) {
							$ext->add('app-announcement-'.$row[0], '_X', '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row[4].':${IVR_CONTEXT},return,1'));
						} else {
							$ext->add('app-announcement-'.$row[0], '_X', '', new ext_goto($row[4]));
						}
					}
					if ($row[7]) {
						$ext->add('app-announcement-'.$row[0], $row[7], '', new ext_goto('s,play'));
					}
				} else {
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_playback($row[2].',noanswer'));
				}

				// if repeat_msg enabled then set exten to t to allow for the key to be pressed, otherwise play message and go
				$exten = $row[7] ? 't':'s';
				if ($row[5]) {
					$ext->add('app-announcement-'.$row[0], $exten, '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row[4].':${IVR_CONTEXT},return,1'));
					if ($row[3] || $row[7])
						$ext->add('app-announcement-'.$row[0], 'i', '', new ext_gotoif('$["x${IVR_CONTEXT}" = "x"]', $row[4].':${IVR_CONTEXT},return,1'));
				} else {
					$ext->add('app-announcement-'.$row[0], $exten, '', new ext_goto($row[4]));
					if ($row[3] || $row[7])
						$ext->add('app-announcement-'.$row[0], 'i', '', new ext_goto($row[4]));
				}
				
			}
		break;
	}
}

function announcement_list() {
	global $db;
	$sql = "SELECT announcement_id, description, recording, allow_skip, post_dest, return_ivr, noanswer, repeat_msg FROM announcement ORDER BY description ";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>Error selecting from announcement");	
	}
	return $results;
}

function announcement_get($announcement_id) {
	global $db;
	$sql = "SELECT announcement_id, description, recording, allow_skip, post_dest, return_ivr, noanswer, repeat_msg FROM announcement WHERE announcement_id = ".addslashes($announcement_id);
	$row = $db->getRow($sql);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Errpr selecting row from announcement");	
	}
	return $row;
}

function announcement_add($description, $recording, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) {
	global $db;
	$sql = "INSERT INTO announcement (description, recording, allow_skip, post_dest, return_ivr, noanswer, repeat_msg) VALUES (".
		"'".addslashes($description)."', ".
		"'".addslashes($recording)."', ".
		"'".($allow_skip ? 1 : 0)."', ".
		"'".addslashes($post_dest)."', ".
		"'".($return_ivr ? 1 : 0)."', ".
		"'".($noanswer ? 1 : 0)."', ".
		"'".addslashes($repeat_msg)."')";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
}

function announcement_delete($announcement_id) {
	global $db;
	$sql = "DELETE FROM announcement WHERE announcement_id = ".addslashes($announcement_id);
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
	
}

function announcement_edit($announcement_id, $description, $recording, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) { 
	global $db;
	$sql = "UPDATE announcement SET ".
		"description = '".addslashes($description)."', ".
		"recording = '".addslashes($recording)."', ".
		"allow_skip = '".($allow_skip ? 1 : 0)."', ".
		"post_dest = '".addslashes($post_dest)."', ".
		"return_ivr = '".($return_ivr ? 1 : 0)."', ".
		"noanswer = '".($noanswer ? 1 : 0)."', ".
		"repeat_msg = '".addslashes($repeat_msg)."' ".
		"WHERE announcement_id = ".addslashes($announcement_id);
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
}

function announcement_check_destinations($dest=true) {
	global $active_modules;

	$destlist = array();
	if (is_array($dest) && empty($dest)) {
		return $destlist;
	}
	$sql = "SELECT announcement_id, post_dest, description FROM announcement ";
	if ($dest !== true) {
		$sql .= "WHERE post_dest in ('".implode("','",$dest)."')";
	}
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	$type = isset($active_modules['announcement']['type'])?$active_modules['announcement']['type']:'setup';

	foreach ($results as $result) {
		$thisdest = $result['post_dest'];
		$thisid   = $result['annoucement_id'];
		$destlist[] = array(
			'dest' => $thisdest,
			'description' => 'Annoucement: '.$result['description'],
			'edit_url' => 'config.php?display=announcement&type='.$type.'&extdisplay='.urlencode($thisid),
		);
	}
	return $destlist;
}

?>
