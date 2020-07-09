<?php
require_once("$CFG->libdir/formslib.php");

class formAcount extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;
        $id        = $this->_customdata['id'];
        $account = null;
        if($id){
            $account =$DB->get_record('custonsmtp_accounts',array('id'=>$id));
            $host =$account->host;
            $name =$account->name;
            $dialylimit = $account->dialylimit;
            $password = $account->password;
        }
        $mform->addElement('header', 'dataform','Conta SMTP');

        //Aqui vou fazer a consulta para colocar no SELECT curso

        $mform->addElement('text', 'name', 'Name:',$name,array('maxlength'=>255));
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'host', 'Host:',$host,array('maxlength'=>255));
        $mform->setType('host', PARAM_TEXT);

        $mform->addElement('text', 'dialylimit', 'Limite Diario:',$dialylimit,array('maxlength'=>4));
        $mform->setType('dialylimit', PARAM_INT);

        $mform->addElement('text', 'priority', 'Prioridade:',$dialylimit,array('maxlength'=>4));
        $mform->setType('priority', PARAM_INT);

        if($id){
            $mform->setConstants('name', $semestre->ano);
            $mform->setConstants('host', $semestre->ano);
            $mform->setConstants('dialylimit', $semestre->idead);
            $mform->setConstants('priority', $semestre->idead);
            $mform->hardFreeze('id');
            $this->add_action_buttons(true, 'Editar', null);
        }
        else{
            $this->add_action_buttons(true, 'Inserir', null);
        }

    }
}


?>
