<?php

function announcement_destinations() {
	// return an associative array with destination and description
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
				$ext->add('app-announcement-'.$row[0], 's', '', new ext_answer(''));
                $ext->add('app-announcement-'.$row[0], 's', '', new ext_wait('1'));
				$ext->add('app-announcement-'.$row[0], 's', '', new ext_noop('Playing announcement '.$row[1]));
				if ($row[3]) {
					// allow skip
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_background($row[2]));
					
					$ext->add('app-announcement-'.$row[0], '_X', '', new ext_noop('User skipped announcement'));
					$ext->add('app-announcement-'.$row[0], '_X', '', new ext_goto($row[4]));
				} else {
					$ext->add('app-announcement-'.$row[0], 's', '', new ext_playback($row[2]));
				}
				$ext->add('app-announcement-'.$row[0], 's', '', new ext_goto($row[4]));
				
			}
		break;
	}
}

function announcement_list() {
	global $db;
	$sql = "SELECT announcement_id, description, recording, allow_skip, post_dest FROM announcement ORDER BY description ";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die($results->getMessage()."<br><br>Error selecting from announcement");	
	}
	return $results;
}

function announcement_get($announcement_id) {
	global $db;
	$sql = "SELECT announcement_id, description, recording, allow_skip, post_dest FROM announcement WHERE announcement_id = ".addslashes($announcement_id);
	$row = $db->getRow($sql);
	if(DB::IsError($row)) {
		die($row->getMessage()."<br><br>Errpr selecting row from announcement");	
	}
	return $row;
}

function announcement_add($description, $recording, $allow_skip, $post_dest) {
	global $db;
	$sql = "INSERT INTO announcement (description, recording, allow_skip, post_dest) VALUES (".
		"'".addslashes($description)."', ".
		"'".addslashes($recording)."', ".
		"'".($allow_skip ? 1 : 0)."', ".
		"'".addslashes($post_dest)."')";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die($result->getMessage().$sql);
	}
}

function announcement_delete($announcement_id) {
	global $db;
	$sql = "DELETE FROM announcement WHERE announcement_id = ".addslashes($announcement_id);
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die($result->getMessage().$sql);
	}
	
}

function announcement_edit($announcement_id, $description, $recording, $allow_skip, $post_dest) { 
	global $db;
	$sql = "UPDATE announcement SET ".
		"description = '".addslashes($description)."', ".
		"recording = '".addslashes($recording)."', ".
		"allow_skip = '".($allow_skip ? 1 : 0)."', ".
		"post_dest = '".addslashes($post_dest)."' ".
		"WHERE announcement_id = ".addslashes($announcement_id);
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die($result->getMessage().$sql);
	}
}

?>