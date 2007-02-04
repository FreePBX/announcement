<?php 
/** Announcments module for freePBX 2.2+
 * Copyright 2006 Greg MacLellan
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

$action = isset($_POST['action']) ? $_POST['action'] :  '';
if (isset($_POST['delete'])) $action = 'delete'; 


$announcement_id = isset($_POST['announcement_id']) ? $_POST['announcement_id'] :  false;
$description = isset($_POST['description']) ? $_POST['description'] :  '';
$recording = isset($_POST['recording']) ? $_POST['recording'] :  '';
$allow_skip = isset($_POST['allow_skip']) ? $_POST['allow_skip'] :  0;
$return_ivr = isset($_POST['return_ivr']) ? $_POST['return_ivr'] :  0;
$post_dest = isset($_POST['post_dest']) ? $_POST['post_dest'] :  '';

if ($_POST['goto0']) {
	// 'ringgroup_post_dest'  'ivr_post_dest' or whatever
	$post_dest = $_POST[ $_POST['goto0'].'0' ];
}


switch ($action) {
	case 'add':
		announcement_add($description, $recording, $allow_skip, $post_dest, $return_ivr);
		needreload();
		redirect_standard();
	break;
	case 'edit':
		announcement_edit($announcement_id, $description, $recording, $allow_skip, $post_dest, $return_ivr);
		needreload();
		redirect_standard('extdisplay');
	break;
	case 'delete':
		announcement_delete($announcement_id);
		needreload();
		redirect_standard();
	break;
}


?> 
</div>

<div class="rnav"><ul>
<?php 
// Eventually I recon the drawListMenu could be built into the new component class thus making
// the relevent page.php file unnessassary

echo '<li><a href="config.php?display=announcement&amp;type=setup">'._('Add Announcement').'</a></li>';

foreach (announcement_list() as $row) {
	echo '<li><a href="config.php?display=announcement&amp;type=setup&amp;extdisplay='.$row[0].'" class="">'.$row[1].'</a></li>';
}

?>
</ul></div>

<div class="content">

<?php
if ($extdisplay) {
	// load
	$row = announcement_get($extdisplay);
	
	$description = $row[1];
	$recording = $row[2];
	$allow_skip = $row[3];
	$post_dest = $row[4];
	$return_ivr = $row[5];

}

?>
<form name="editAnnouncement" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkAnnouncement(editAnnouncement);">
			<input type="hidden" name="extdisplay" value="<?php echo $extdisplay; ?>">
			<input type="hidden" name="announcement_id" value="<?php echo $extdisplay; ?>">
			<input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edit' : 'add'); ?>">
			<table>
			<tr><td colspan="2"><h5><?php  echo ($extdisplay ? _("Edit Announcement") : _("Add Announcement")) ?><hr></h5></td></tr>
			<tr>
				<td><a href="#" class="info"><?php echo _("Description")?>:<span><?php echo _("The name of this announcement")?></span></a></td>
				<td><input size="15" type="text" name="description" value="<?php  echo $description; ?>"></td>
			</tr>

<?php if(function_exists('recordings_list')) { //only include if recordings is enabled?>
	<tr>
		<td><a href="#" class="info"><?php echo _("Recording")?><span><?php echo _("Message to be played.<br>To add additional recordings use the \"System Recordings\" MENU to the left")?></span></a></td>
		<td>
			&nbsp;&nbsp;<select name="recording"/>
			<?php
				$tresults = recordings_list();
				$default = (isset($recording) ? $recording : '');
				if (isset($tresults[0])) {
					foreach ($tresults as $tresult) {
						echo '<option value="'.$tresult[2].'"'.($tresult[2] == $default ? ' SELECTED' : '').'>'.$tresult[1]."</option>\n";
					}
				}
			?>
			</select>
		</td>
	</tr>
<?php } ?>
	<tr>
		<td><a href="#" class="info"><?php echo _("Allow Skip")?><span><?php echo _("If the caller is allowed to press a key to skip the message.")?></span></a></td>
		<td><input type="checkbox" name="allow_skip" value="1" <?php echo ($allow_skip ? 'CHECKED' : ''); ?> /></td>
	</tr>
	<tr>
		<td><a href="#" class="info"><?php echo _("Return to IVR")?><span><?php echo _("If this announcement came from an IVR and this box is checked, the destination below will be ignored and instead it will return to the calling IVR. Otherwise, the destinatino below will be taken.")?></span></a></td>
		<td><input type="checkbox" name="return_ivr" value="1" <?php echo ($return_ivr ? 'CHECKED' : ''); ?> /></td>
	</tr>
	
	<tr><td colspan="2"><br><h5><?php echo _("Destination after playback")?>:<hr></h5></td></tr>

<?php 
//draw goto selects
echo drawselects($post_dest,0);
?>
			
			<tr>
			<td colspan="2"><br><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>">
			<?php if ($extdisplay) { echo '&nbsp;<input name="delete" type="submit" value="'._("Delete").'">'; } ?>
			</td>		
			
			</tr>
			</table>
			</form>
			
			
<script language="javascript">
<!--

function checkAnnouncement(theForm) {
	var msgInvalidDescription = "<?php echo _('Invalid description specified'); ?>";

	// set up the Destination stuff
	setDestinations(theForm, '_post_dest');

	// form validation
	defaultEmptyOK = false;	
	if (isEmpty(theForm.description.value))
		return warnInvalid(theForm.description, msgInvalidDescription);

	if (!validateDestinations(theForm, 1, true))
		return false;

	return true;
}
//-->
</script>
