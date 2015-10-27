<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2006-2014 Schmooze Com Inc.
//
$request = $_REQUEST;
$heading = _("Announcement");
$request["view"] = !empty($request["view"]) ? $request["view"] : '';
//get unique queues
switch($request["view"]){
	case "form":
		if($request['extdisplay']){
			$heading .= _(": Edit");
		}else{
			$heading .= _(": Add");
		}
		$content = load_view(__DIR__.'/views/form.php', array('request' => $request, 'amp_conf' => $amp_conf));
	break;
	default:
		$content = load_view(__DIR__.'/views/grid.php');
	break;
}

?>
<div class="container-fluid">
	<h1><?php echo $heading ?></h1>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<div class="display <?php echo !empty($_REQUEST['view']) ? "full" : "no"?>-border">
						<?php echo $content ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>




<script language="javascript">
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
</script>
