<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules;
class Announcement extends \FreePBX_Helpers implements \BMO {

	private $freepbx;
	public function __construct($freepbx = null) {
		parent::__construct($freepbx);
		$this->freepbx = $freepbx;
		$this->db = $this->freepbx->Database;
	}

	public function getAnnouncements() {
		$sql = "SELECT announcement_id, description, recording_id, allow_skip, post_dest, return_ivr, noanswer, repeat_msg FROM announcement";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAnnouncementByID($id) {
		$sql = "SELECT announcement_id, description, recording_id, allow_skip, post_dest, return_ivr, noanswer, repeat_msg FROM announcement WHERE announcement_id = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($id));
		return $sth->fetch(\PDO::FETCH_ASSOC);


		$row = $db->getRow($sql,DB_FETCHMODE_ASSOC);
		if(DB::IsError($row)) {
			die_freepbx($row->getMessage()."<br><br>Error selecting row from announcement");
		}
		// Added Associative query above but put positional indexes back to maintain backward compatibility
		//
		$i = 0;
		if(!empty($row) && is_array($row)) {
			foreach ($row as $item) {
				$row[$i] = $item;
				$i++;
			}
			return $row;
		} else {
			return array();
		}
	}

	/**
	 * Ajax Request
	 * @param string $req     The request type
	 * @param string $setting Settings to return back
	 */
	public function ajaxRequest($req, $setting){
		switch($req){
			case "getData":
			case "getJSON":
				return true;
			default:
				return false;
		}
	}

	/**
	 * Handle AJAX
	 */
	public function ajaxHandler(){
		$request = $_REQUEST;
		switch($request['command']){
			case "getData":
			break;
			case "getJSON":
				return $this->getAnnouncements();
			default:
			break;
		}
	}

	public function getActionBar($request) {
		$buttons = array();
		if($request['display'] == 'announcement'){
			$buttons = array(
				'delete' => array(
					'name' => 'delete',
					'id' => 'delete',
					'value' => _('Delete')
				),
				'reset' => array(
					'name' => 'reset',
					'id' => 'reset',
					'value' => _('Reset')
				),
				'submit' => array(
					'name' => 'submit',
					'id' => 'submit',
					'value' => _('Submit')
				)
			);
			if (empty($request['extdisplay'])) {
				unset($buttons['delete']);
			}
			if(empty($_GET['view']) || $_GET['view'] != 'form'){
				$buttons = array();
			}
		}
		return $buttons;
	}

	public function install() {
		//Tables added via module.xml
	}

	public function uninstall() {
		$sql = 'DROP TABLE announcement';
		$stmt = $this->db->prepare($sql);
		return $stmt->execute();
	}

	public function backup($backup) {
		//Unused See Backup.php
	}

	public function restore($backup) {
		//Unused See Restore.php
	}

	public function doTests($db) {
		return true;
	}
	public function addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) {
		$defaults = [
			'allow_skip' => ($allow_skip) ? 1 : 0,
			'noanswer' => ($noanswer) ? 1 : 0,
			'return_ivr' => ($return_ivr) ? 1 : 0,
		];
		foreach($defaults as $key => $value) {
			if(is_null($$key)) {
				$$key = $value;
			}
		}
		$sql = "INSERT INTO announcement  (description, recording_id, allow_skip, post_dest, return_ivr, noanswer, repeat_msg) VALUES  (:description, :recording_id, :allow_skip, :post_dest, :return_ivr, :noanswer, :repeat_msg)";
		$insert = [
			':description' => $description,
			':recording_id' => $recording_id,
			':allow_skip' => $allow_skip,
			':post_dest' => $post_dest,
			':return_ivr' => $return_ivr,
			':noanswer' => $noanswer,
			':repeat_msg' => $repeat_msg
		];
		$stmt = $this->db->prepare($sql);
		$stmt->execute($insert);
		return $this->freepbx->Database->lastInsertId();
	}

	public function deleteAnnouncement($id){
		$sql = "DELETE FROM announcement WHERE announcement_id = :id";
		$stmt = $this->db->prepare($sql);
		return $stmt->execute([':id' => $id]);
	}

	public function editAnnouncement($announcement_id,$description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg){
	$defaults = [
		'allow_skip' => ($allow_skip) ? 1 : 0,
		'noanswer' => ($noanswer) ? 1 : 0,
		'return_ivr' => ($return_ivr) ? 1 : 0,
	];
	foreach($defaults as $key => $value) {
		if(is_null($$key)) {
			$$key = $value;
		}
	}
	$sql = "UPDATE announcement SET
		`description` = :description,
		`recording_id` = :recording_id,
		`allow_skip` = :allow_skip,
		`post_dest` = :post_dest,
		`return_ivr` = :return_ivr,
		`noanswer` = :noanswer,
		`repeat_msg` = :repeat_msg
		WHERE `announcement_id` = :announcement_id";
		$insert = [
			':description' => $description,
			':recording_id' => $recording_id,
			':allow_skip' => $allow_skip,
			':post_dest' => $post_dest,
			':return_ivr' => $return_ivr,
			':noanswer' => $noanswer,
			':repeat_msg' => $repeat_msg,
			':announcement_id' => $announcement_id
		];
		$stmt = $this->db->prepare($sql);
		return $stmt->execute($insert);
	}
	public function doConfigPageInit($page) {
		$request = $_REQUEST;
		$action = isset($request['action']) ? $request['action'] :  '';
		if (isset($request['delete'])){
			$action = 'delete';
		}
		$announcement_id = isset($request['announcement_id']) ? $request['announcement_id'] :  false;
		$description = isset($request['description']) ? $request['description'] :  '';
		$recording_id = isset($request['recording_id']) ? $request['recording_id'] :  '';
		$allow_skip = isset($request['allow_skip']) ? $request['allow_skip'] :  0;
		$return_ivr = isset($request['return_ivr']) ? $request['return_ivr'] :  0;
		$noanswer = isset($request['noanswer']) ? $request['noanswer'] :  0;
		$post_dest = isset($request['post_dest']) ? $request['post_dest'] :  '';
		$repeat_msg = isset($request['repeat_msg']) ? $request['repeat_msg'] :  '';

		if (isset($request['goto0']) && $request['goto0']) {
			// 'ringgroup_post_dest'  'ivr_post_dest' or whatever
			$post_dest = $request[ $request['goto0'].'0' ];
		}


		switch ($action) {
			case 'add':
				$this->addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
				needreload();
			break;
			case 'edit':
				$this->editAnnouncement($announcement_id, $description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
				needreload();
			break;
			case 'delete':
				$this->deleteAnnouncement($_REQUEST['extdisplay']);
				needreload();
			break;
			default:
			break;
		}
	}

	public function getRightNav($request) {
		if(isset($_GET['view']) && $_GET['view'] == 'form'){
		    return load_view(__DIR__."/views/rnav.php",array());
		}
	}
}
