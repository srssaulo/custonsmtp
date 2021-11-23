<?php
require_once("$CFG->libdir/formslib.php");

class formEnvia extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;
        $id        = $this->_customdata['id'];

        $mform->addElement('header', 'dataform','Envio de Emails');
        
        $cursous = $DB->get_records("course",array('visible'=>1));

        $arrayCursos = array();
        foreach($cursous as $curso){
            if($curso->id ==1){
                continue;
            }
            $arrayCursos[$curso->id] = $curso->fullname;

        }

        $mform->addElement('select', 'course', 'Curso:', $arrayCursos);
        $mform->setType('course', PARAM_INT);


        $roles = $DB->get_records_sql("SELECT id,name,shortname from {role} where id in (SELECT roleid from {role_assignments})");

        $arrayRole = array();
        foreach($roles as $role){
            $arrayRole[$role->id] = $role->name."({$role->shortname})";

        }

        $mform->addElement('select', 'role', 'Papel:', $arrayRole);
        $mform->setType('role', PARAM_INT);

        
        $accounts = $DB->get_records("custonsmtp_accounts");

        $arrayAccount = array();
        foreach($accounts as $account){
            $arrayAccount[$account->id] = $account->name;

        }

        $mform->addElement('select', 'account', 'Conta para envio:', $arrayAccount);
        $mform->setType('account', PARAM_INT);

        $mform->addElement('text', 'title', 'Titulo:',array('maxlength'=>255));
        $mform->setType('host', PARAM_TEXT);

        $mform->addElement('textarea', 'body', 'Menagem:');
        $mform->setType('username', PARAM_TEXT);

        $selectPriority = $mform->addElement('select', 'priority', "Prioridade:", array('1', '2', '3','4'));
        $mform->addHelpButton('priority', 'priority', 'local_custonsmtp');
        $mform->setType('priority', PARAM_INT);

        $this->add_action_buttons(true, 'Enviar', null);

    }
}


?>
