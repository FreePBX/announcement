<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
//?>
<a href="config.php?display=announcement" class="list-group-item <?php echo (empty($request['view'])? 'hidden':'')?>"><i class="fa fa-list"></i>&nbsp; <?php echo _("List Announcemets") ?></a>
<a href="config.php?display=announcement&amp;view=form" class="list-group-item <?php echo (!empty($request['view']) && $request['view'] == 'form'? 'hidden':'')?>" ><i class="fa fa-plus"></i>&nbsp; <?php echo _("Add Announcemet") ?></a>
