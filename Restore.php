<?php
namespace FreePBX\modules\Announcement;

class Restore{

  __construct($restoreobj=null,$freepbx){
    $this->restoreobj = $restoreobj;
    $this->freepbx = $freepbx;
  }
  public function runRestore(){
    $deps = $this->restoreobj->getDependecies('Core');
    $configs = $this->restoreobj->getConfigs();
    $this->freepbx->announcements->resetModule(true);
    foreach($configs as $config){
      extract($config);
      $this->freepbx->announcements->addAnnouncemet($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
    }
  }
}
