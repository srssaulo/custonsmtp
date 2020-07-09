<?php
require_once('../../config.php');
require_once('locallib.php');
require_once('formAcount.php');

global $DB;

if(!is_siteadmin()){
    redirect($CFG->wwwroot,'Acesso não autorizado');
}

$id = optional_param('id', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);
if (count($_POST)>0) {
    $data = (object)$_POST;
    CriaAccount($data);
}


$PAGE->set_url('/local/custonsmtp/accounts.php');
$PAGE->set_pagelayout('default');

$PAGE->set_title('Gerenciamento de Contas');
$PAGE->set_heading('Gerenciamento de Contas');
echo $OUTPUT->header();

$context = context_system::instance();
if($edit){
    $mform = new formSemestre(null,array('id'=>$id));
    if ($mform->is_cancelled()) {
        redirect('accounts.php');
    }
    $mform->display();
}
else{
    $table = new html_table();
    $table->head = array('id', 'Nome','host','');
    $accounts = $DB->get_records("custonsmtp_accounts");

    foreach($accounts as $account){
        $table->data[] = array($account->id, $account->name,$account->host,  "<a href=\"accounts.php?id={$sem->id}&edit=1\">Editar");
    }
    echo html_writer::table($table);
    echo "<button><a href='accounts.php?edit=1'>Adicionar Conta</a></button>";
}


echo $OUTPUT->footer();

