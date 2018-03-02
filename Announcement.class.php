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
	public function resetModule($confirm = false){
		if($confirm !== true){
			return false; 
		}
		$sql = "TRUNCATE announcement";
		$sth = $this->db->prepare($sql);
		needreload();
		return $sth->execute();
	}
	public function getAnnouncements() {
		$sql = "SELECT announcement_id, description, recording_id, allow_skip, post_dest, return_ivr, noanswer, repeat_msg FROM announcement";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(\PDO::FETCH_ASSOC);
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
	}

	public function uninstall() {
	}

	public function backup($backup) {

	}

	public function restore($backup) {
	}

	public function doTests($db) {
		return true;
	}
	public function addAnnouncement($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg) {
		$sql = "INSERT INTO announcement  (description, recording_id, allow_skip, post_dest, return_ivr, noanswer, repeat_msg) VALUES  (:description, :recording_id, :allow_skip, :post_dest, :return_ivr, :noanswer, :repeat_msg)";
		$insert = [
			':description' => $description,
			':recording_id' => $recording_id,
			':allow_skip' => $allow_skip,
			':post_dest' => $post_deat,
			':return_ivr' => $return_ivr,
			':noanswer' => $noanswer,
			':repeat_msg' => $repeat_msg
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
				announcement_edit($announcement_id, $description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
				needreload();
			break;
			case 'delete':
				announcement_delete($_REQUEST['extdisplay']);
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
