<?php
namespace FreePBX\modules\Announcement;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore(){
    $configs = $this->getConfigs();
    $this->processData($configs);
  }
  public function processLegacy($pdo, $data, $tables, $unknownTables, $tmpfiledir){
    $tables = array_flip($tables + $unknownTables);
    if(!isset($tables['announcement'])){
      return $this;
    }
    $announcements = $this->FreePBX->Announcement;
    $announcements->setDatabase($pdo);
    $data = $announcements->getAnnouncements();
    $announcements->resetDatabase();
    $this->processData($data);
    return $this;
  }
  
  public function processData($configs){
    $fields = [
      'description',
      'recording_id',
      'allow_skip',
      'post_dest',
      'return_ivr',
      'noanswer',
      'repeat_msg',
    ];
    foreach ($configs as $config) {
        foreach ($fields as $field) {
          isset($config[0][$field])? $$field = $config[0][$field] : $$field = null;
        }
        dbug([$description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg]);
        $this->FreePBX->Announcement->addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
    }
  }
}
