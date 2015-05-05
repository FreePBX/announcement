<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
//
//
?>
<div id="toolbar-all">
	<button id="remove-all" class="btn btn-danger btn-remove" data-type="extensions" disabled data-section="all">
		<i class="glyphicon glyphicon-remove"></i> <span><?php echo _('Delete')?></span>
	</button>
</div>
<table data-toolbar="#toolbar-all" data-toggle="table" data-pagination="true" data-search="true" class="table table-striped" id="table-all">
<thead>
	<tr>
		<th data-sortable="true"><?php echo _("ID")?></th>
		<th data-sortable="true"><?php echo _("Description")?></th>
		<th><?php echo _("Actions")?></th>
	</tr>
</thead>
<tbody>
	<?php foreach (announcement_list() as $row) {?>
		<tr>
			<td><?php echo $row['announcement_id']?></td>
			<td><?php echo $row['description']?></td>
			<td><a href="?display=announcement&amp;view=form&amp;extdisplay=<?php echo $row['announcement_id']?>"><i class="fa fa-edit"></i></a></td>
		</tr>
	<?php } ?>
</tbody>
</table>
