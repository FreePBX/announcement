<?php
namespace FreePBX\modules\Announcement;

class Backup{

  public function __construct($backupobj=null,$freepbx){
    $this->backupobj = $backupobj;
    $this->freepbx = $freepbx;
  }

  public function runBackup($id,$transaction){
    $this->backupobj->addDependency('Core');
    $this->backupobj->addDependency('Recordings');
    $this->backupobj->addConfigs($this->freepbx->Announcement->getAnnouncements());
  }
}