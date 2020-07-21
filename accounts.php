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
if($edit){
    $mform = new formAcount(null,array('id'=>$id));
    if ($mform->is_cancelled()) {
        redirect('accounts.php');
    }
    $mform->display();
}
else{
    if (count($_POST)>0) {
        $data = (object)$_POST;
        CriaAccount($data);
    }
    elseif($delete){
        DeletaAccount($id);
    }
    $table = new html_table();
    $table->head = array('id', 'Nome','host','');
    $accounts = $DB->get_records("custonsmtp_accounts");

    foreach($accounts as $account){
        $table->data[] = array($account->id, $account->name,$account->host,  "<a href=\"accounts.php?id={$account->id}&edit=1\">Editar</a> <a href=\"accounts.php?id={$account->id}&delete=1\">Deletar</a>");
    }
    echo html_writer::table($table);
    echo $OUTPUT->single_button("accounts.php?edit=1", "Adicionar Conta", 'get');
}


echo $OUTPUT->footer();

