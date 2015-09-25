<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2015 Sangoma Technologies.
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules;
class Announcement extends \FreePBX_Helpers implements \BMO  {

	private $freepbx;
	public function __construct($freepbx = null) {
		parent::__construct($freepbx);
		$this->freepbx = $freepbx;
	}

	public function getActionBar($request) {
		$buttons = array();

		switch($request['display']) {
			case 'announcement':
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
				if(empty($request['view']) || $request['view'] != 'form'){
					$buttons = array();
				}
			break;
		}
		return $buttons;
	}

	public function install() {
	}

	public function uninstall() {
	}

	public function backup() {
	}

	public function restore($backup) {
	}

	public function doTests($db) {
		return true;
	}

	public function doConfigPageInit($page) {
		$request = $_REQUEST;
		$action = isset($request['action']) ? $request['action'] :  '';
		if (isset($request['delete'])) $action = 'delete';

		$announcement_id = isset($request['announcement_id']) ? $request['announcement_id'] :  false;
		$view = isset($request['view']) ? $request['view'] :  'form';
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
				$_REQUEST['extdisplay'] = announcement_add($description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
				needreload();
				$this->freepbx->View->redirect_standard('extdisplay', 'view');
			break;
			case 'edit':
				announcement_edit($announcement_id, $description, $recording_id, $allow_skip, $post_dest, $return_ivr, $noanswer, $repeat_msg);
				needreload();
				$this->freepbx->View->redirect_standard('extdisplay', 'view');
			break;
			case 'delete':
				announcement_delete($_REQUEST['extdisplay']);
				needreload();
				$this->freepbx->View->redirect_standard();
			break;
		}
	}
}