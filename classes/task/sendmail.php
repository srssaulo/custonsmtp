<?php
namespace local_custonsmtp\task;



class sendmail extends \core\task\scheduled_task {
	public function get_name() {
		// Shown in admin screens
		return "Envia os emails pelas contas cadastradas";
	}

	public function execute() {
		global $CFG;
		$ctx = stream_context_create(array('http'=>
		    array(
		        'timeout' => 1200,  //1200 Seconds is 20 Minutes
		    )
		));

		file_get_contents($CFG->wwwroot.'/local/avasplugin/classes/log/testeSegets.php',false,$ctx);
	}

}