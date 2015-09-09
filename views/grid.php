<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
//
//
$destlist = \FreePBX::Modules()->getDestinations();
?>
<div id="toolbar-all">
	<a href="config.php?display=announcement&amp;view=form" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo _('Add')?></a>
</div>
<table data-toolbar="#toolbar-all" data-toggle="table"  data-maintain-selected="true" data-show-columns="true" data-show-toggle="true" data-toggle="table" data-pagination="true" data-search="true"  id="table-all">
<thead>
	<tr>
		<th data-sortable="true"><?php echo _("Description")?></th>
		<th data-sortable="true"><?php echo _("Destination")?></th>
		<th><?php echo _("Actions")?></th>
	</tr>
</thead>
<tbody>
	<?php foreach (announcement_list() as $row) {
		$postd = isset($destlist[$row['post_dest']]['description'])?$destlist[$row['post_dest']]['description']:$row['post_dest'];
		$dest = $row['return_ivr'] == 1? _("Return to IVR"):$postd;
		?>
		<tr>
			<td><?php echo $row['description']?></td>
			<td><?php echo $dest?></td>
			<td>
				<a href="?display=announcement&amp;view=form&amp;extdisplay=<?php echo $row['announcement_id']?>"><i class="fa fa-edit"></i></a>
				<a href="?display=announcement&amp;action=delete&amp;extdisplay=<?php echo $row['announcement_id']?>" class="delAction"><i class="fa fa-trash"></i></a>
			</td>
		</tr>
	<?php } ?>
</tbody>
</table>
