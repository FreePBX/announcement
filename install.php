<?php

global $db;

// Version 0.3 adds auto-return to IVR
$sql = "SELECT return_ivr FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD return_ivr TINYINT(1) NOT NULL DEFAULT 0;";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die($result->getDebugInfo()); }
}

// Version 0.4 adds auto-return to IVR
$sql = "SELECT noanswer FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD noanswer TINYINT(1) NOT NULL DEFAULT 0;";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die($result->getDebugInfo()); }
}

?>
