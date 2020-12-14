<?php
namespace local_custonsmtp\task;

use stdClass;

class sendmail extends \core\task\scheduled_task {
	public function get_name() {
		// Shown in admin screens
		return "Envia os emails pelas contas cadastradas";
	}

	public function execute() {
		ini_set("memory_limit", "-1");
		set_time_limit(-1);
		global $DB;

		$accountAray = $DB->get_records('custonsmtp_accounts');
		$maxmailsend= get_config('local_custonsmtp', 'maxemailsend');
		foreach($accountAray as $account){
			$account->maxmailsend = $account->dialylimit?$account->dialylimit:$maxmailsend;
			$account->mailsend = 0;
		}
		$limitall= get_config('local_custonsmtp', 'limityall');
		$priority= get_config('local_custonsmtp', 'priority');
		$totalmailsend = 0;
		if($priority){
			if($accountAray[$priority]){
				$priorityMailsTosend = $DB->get_records_sql("SELECT * from {custonsmtp_email} 
				where account={$priority} and timesend is null
				order by timecreated");
				$localmailsend = 0;
				$limitebraked = false;
				foreach($priorityMailsTosend as $mail){
					if($this->process_queue($mail,$accountAray)){
						$localmailsend++;
						$totalmailsend++;
						if($localmailsend>$maxmailsend){
							$limitebraked = true;
							break;
						}
					}
				}
				$accountAray[$priority]->mailsend = $localmailsend;
				if($limitall&&$limitebraked){//somente prioridades foram enviados
					return;
				}
			}
			else{//erro

			}
		}
		
		$mailsTosend = $DB->get_records_sql("SELECT ce.*,ca.priority from {custonsmtp_email} ce
			INNER JOIN {custonsmtp_accounts} ca on ca.id=ce.account
			where timesend is null
			order by ca.priority DESC ,ce.timecreated");
		foreach($mailsTosend as $mail){
			if($accountAray[$mail->account]&&!$accountAray[$mail->account]->limitebraked){
				if($this->process_queue($mail,$accountAray[$mail->account])){
					$totalmailsend++;
					$accountAray[$mail->account]->mailsend++;
					if($accountAray[$mail->account]->mailsend>$accountAray[$mail->account]->maxmailsend){
						$accountAray[$mail->account]->limitebraked = true;
					}
					if($limitall&&$totalmailsend>$maxmailsend){//ja enviaram todos
						return;
					}
				}
			}

		}
	}

	function process_queue($mailOb,$accountOb){
		global $CFG;

		require_once $CFG->dirroot.'/lib/phpmailer/moodle_phpmailer.php';
		$mail = new \moodle_phpmailer();
		$mail->isSMTP();
		//$mail->SMTPDebug = true;
		// Specify main and backup servers.
		$mail->Host          = $accountOb->host;
		// Specify secure connection protocol.
		$mail->SMTPSecure    = $accountOb->security;
		
		// Use SMTP authentication.
		$mail->SMTPAuth = true;
		$mail->Username = $accountOb->username;
		$mail->Password = $accountOb->password;
			
		$mail->Sender = $mailOb->from_mail;
		if($mailOb->from_name){
			$mail->FromName = $mailOb->from_name;
		}
		if($mailOb->replyto){
			$mail->addReplyTo($mailOb->replyto);
		}
		else{
			$mail->addReplyTo($mailOb->from_mail);
		}
		$mail->From =$mailOb->from_mail;
		$mail->Subject = $mailOb->title;
		$mail->WordWrap = 79;
		$mail->isHTML(true);
		$mail->Encoding = 'quoted-printable';
		if($mailOb->body_html){
			$messagehtml = $mailOb->body_html;
		}else{
			$messagehtml = trim(text_to_html($mailOb->body));
		}
		$mail->Body    =  $messagehtml;
		$mail->AltBody =  "\n$mailOb->body\n";
		$toArray = explode(',',$mailOb->to_adress);
		
		foreach($toArray as $to){
			if($to)
				$mail->addAddress($to);
		}
		
		//adicoina cc
		$cArray = explode(',',$mailOb->cc);
		
		foreach($cArray as $to){
			if($to)
				$mail->addCC($to);
		}
		//adociona  bcc
		$bccArray = explode(',',$mailOb->bcc);
		
		foreach($bccArray as $to){
			if($to)
				$mail->addBCC($to);
		}
		
		if($mail->send() == false) { //erro no envio
			return false;
		} else { 
			global $DB;
			$update = new stdClass();
			$update->id = $mailOb->id;
			$update->timesend = time();
			
			$DB->update_record('custonsmtp_email',$update);
			return true;
		}
	}
}