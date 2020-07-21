<?php
namespace local_custonsmtp\task;

use stdClass;

class sendmail extends \core\task\scheduled_task {
	public function get_name() {
		// Shown in admin screens
		return "Envia os emails pelas contas cadastradas";
	}

	public function execute() {
		global $DB;

		$accountAray = $DB->get_records('custonsmtp_accounts');
		foreach($accountAray as $account){
			$account->maxmailsend = 0;
		}
		$maxmailsend= get_config('local_custonsmtp', 'maxemailsend');
		$limitall= get_config('local_custonsmtp', 'limityall');
		$priority= get_config('local_custonsmtp', 'priority');
		$totalmailsend = 0;
		if($priority){
			if($accountAray[$priority]){
				$priorityMailsTosend = $DB->get_records_sql("SELECT * from {custonsmtp_email} 
				where account={$priority} and timesend is null
				order by timecreated"));
				$localmailsend = 0;
				$limitebraked = false;
				foreach($priorityMailsTosend as $mail){
					if($this->process_queue($mail,$accountAray)){
						$localmailsend++;
						$totalmailsend++;
						$update = new stdClass();
						$update->id = $mail->id;
						$update->timesend = time();
						$DB->update_record('custonsmtp_email',$mail);
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
		
		$mailsTosend = $DB->get_records_sql("SELECT * from {custonsmtp_email} 
			where timesend is null
			order by timecreated");
		foreach($mailsTosend as $mail){
			if($accountAray[$mail->account]&&$accountAray[$mail->account]->mailsend<$maxmailsend){
				if($this->process_queue($mail,$accountAray[$mail->account])){
					$totalmailsend++;
					if($limitall&&$totalmailsend>$maxmailsend){//ja enviaram todos
						return;
					}
					$accountAray[$mail->account]->mailsend++;
				}
			}

		}
	}

	function process_queue($mailOb,$accountOb){
		global $CFG;

		require_once $CFG->dirroot.'/lib/phpmailer/moodle_phpmailer.php';
		$mail = new moodle_phpmailer();
		$mail->isSMTP();
		// Specify main and backup servers.
		$mail->Host          = $accountOb->host;
		// Specify secure connection protocol.
		$mail->SMTPSecure    = $accountOb->security;
		
		// Use SMTP authentication.
		$mail->SMTPAuth = true;
		$mail->Username = $accountOb->username;
		$mail->Password = $accountOb->password;
			
		$mail->Sender = $mailOb->from;
		if($mailOb->fromName){
			$mail->FromName = $mailOb->fromName;
		}
		if($mailOb->replyto){
			$mail->addReplyTo($mailOb->replyto);
		}
		$mail->From = $mailOb->from;
		$mail->Subject = $mailOb->header;
		$mail->WordWrap = 79;
		$mail->isHTML(true);
		$mail->Encoding = 'quoted-printable';
		if($mailOb->bodyHTML){
			$messagehtml = $mailOb->bodyHTML;
		}
		$messagehtml = trim(text_to_html($mailOb->body));
		$mail->Body    =  $mailOb->body;
		$mail->AltBody =  "\n$mailOb->body\n";
		$toArray = explode($mailOb->body,',');
		foreach($toArray as $to){
			$mail->addAddress($to);
		}
		
		//adicoina cc
		$cArray = explode($mailOb->cc,',');
		foreach($cArray as $to){
			$mail->addCC($to);
		}
		//adociona  bcc
		$bccArray = explode($mailOb->bcc,',');
		foreach($bccArray as $to){
			$mail->addBCC($to);
		}
		
		
		if($mail->send() == false) { //erro no envio
			return false;
		} else { 
			return true;
		}
	}
}