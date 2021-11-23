<?php
require_once('../../config.php');
require_once('locallib.php');
require_once('formAcount.php');

global $DB,$PAGE;

require_login();
$PAGE->set_context(context_system::instance());
if(!is_siteadmin()){
    redirect($CFG->wwwroot,'Acesso não autorizado');
}

$id = optional_param('id', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);


$PAGE->set_url('/local/custonsmtp/accounts.php');
$PAGE->set_pagelayout('admin');

$PAGE->set_title('Gerenciamento de Contas');
$PAGE->set_heading('Gerenciamento de Contas');
echo $OUTPUT->header();

$context = context_system::instance();
$mform = new formEnvio(null,array('id'=>$id));

$mform->display();

if (count($_POST)>0) {
    $data = (object)$_POST;
    EnviaEmails($data);
}


echo $OUTPUT->footer();

