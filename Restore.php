<?php
namespace FreePBX\modules\Announcement;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
public function runRestore(){
    $configs = $this->getConfigs();
    foreach($configs as $config){
      extract($config[0]);
      $this->FreePBX->Announcement->addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
    }
  }
}
