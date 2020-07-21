<?php
require_once("$CFG->libdir/formslib.php");

class formAcount extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;
        $id        = $this->_customdata['id'];

        $mform->addElement('header', 'dataform','Conta SMTP');
        
        $mform->addElement('text', 'name', 'Name:',array('maxlength'=>255));
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'host', 'Host:',array('maxlength'=>255));
        $mform->setType('host', PARAM_TEXT);

        $mform->addElement('text', 'username', 'Username:',array('maxlength'=>255));
        $mform->setType('username', PARAM_TEXT);

        $mform->addElement('passwordunmask', 'password', 'Password:',array('maxlength'=>255));
        $mform->setType('password', PARAM_TEXT);

        $selectSecurity =$mform->addElement('select', 'security', "Segurança:", array(''=>"Nenhuma", 'ssl'=>'SSL', 'tls'=>"TLS"));
        $mform->setType('security', PARAM_TEXT);

        $mform->addElement('text', 'dialylimit', 'Limite Diario:', array('maxlength'=>4));
        $mform->setType('dialylimit', PARAM_INT);

        $selectPriority = $mform->addElement('select', 'priority', "Prioridade:", array('1', '2', '3','4'));
        $mform->addHelpButton('priority', 'priority', 'local_custonsmtp');
        $mform->setType('priority', PARAM_INT);
    
        if($id){
            $account =$DB->get_record('custonsmtp_accounts',array('id'=>$id));
            $mform->addElement('hidden', 'id', $id);
            $mform->setType('id', PARAM_INT);
            $mform->hardFreeze('id');
            $mform->setDefault('name', $account->name);
            $mform->setDefault('host', $account->host);
            $mform->setDefault('username', $account->username);
            $mform->setDefault('password', $account->password);
            $mform->setDefault('dialylimit', $account->dialylimit);
            $selectSecurity->setSelected($account->security);
            $selectPriority->setSelected($account->priority);

            $this->add_action_buttons(true, 'Editar', null);
        }
        else{
            $this->add_action_buttons(true, 'Inserir', null);
        }

    }
}


?>
