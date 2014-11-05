<?php
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules;
class Announcement extends \FreePBX_Helpers implements \BMO  {

	public function __construct($freepbx = null) {
		parent::__construct($freepbx);
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
	}
}
