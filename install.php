<?php

global $db;

// Version 2.1 upgrade. Add support for ${DIALOPTS} override, playing MOH
$sql = "SELECT return_ivr FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD return_ivr TINYINT(1) NOT NULL DEFAULT 0;";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die($result->getDebugInfo()); }
}

?>
