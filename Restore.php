<?php
namespace FreePBX\modules\Announcement;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore(){
		$configs = $this->getConfigs();
		$this->processData($configs);
	}

	public function processLegacy($pdo, $data, $tables, $unknownTables){
		$tables = array_flip($tables + $unknownTables);
		if(!isset($tables['announcement'])){
			$this->log("No announcement tables");
			return;
		}

		$rows = $pdo->query("SELECT * FROM announcement")->fetchAll(\PDO::FETCH_ASSOC);
		$this->processData($rows);
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
		$sth = $this->FreePBX->Database->prepare("INSERT INTO announcement (`announcement_id`, `description`, `recording_id`, `allow_skip`, `post_dest`, `return_ivr`, `noanswer`, `repeat_msg`) VALUES (:announcement_id, :description, :recording_id, :allow_skip, :post_dest, :return_ivr, :noanswer, :repeat_msg)");
		foreach ($configs as $config) {
			foreach ($fields as $field) {
				isset($config[$field])? $$field = $config[$field] : $$field = null;
			}
			$sth->execute([
				":announcement_id" => $config['announcement_id'],
				":description" => $config['description'],
				":recording_id" => $config['recording_id'],
				":allow_skip" => $config['allow_skip'],
				":post_dest" => $config['post_dest'],
				":return_ivr" => $config['return_ivr'],
				":noanswer" => $config['noanswer'],
				":repeat_msg" => $config['repeat_msg']
			]);
		}
	}
}
