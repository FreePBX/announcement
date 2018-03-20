<?php
namespace FreePBX\modules\Announcement;

class Restore{

  public function __construct($restoreobj=null,$freepbx){
    $this->restoreobj = $restoreobj;
    $this->freepbx = $freepbx;
  }
  public function runRestore(){
    $configs = $this->restoreobj->getConfigs();
    foreach($configs as $config){
      extract($config[0]);
      $this->freepbx->Announcement->addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
    }
  }
}
