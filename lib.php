<?php
//this file will rewrite one of the functions of phpmailer
$overwrite= get_config('local_custonsmtp', 'oberwritesendmail');
if(function_exists("override_function")&$overwrite){
    rename_function('email_to_user', 'old_email_to_user');
    override_function("email_to_user",'$user, $from, $subject, $messagetext, $messagehtml, $attachment, $attachname,
    $usetrueaddress, $replyto, $replytoname, $wordwrapwidth','
        global $CFG;
        require_once($CFG->dirroot."/local/custonsmtp.php");
        email_to_user_override($user, $from, $subject, $messagetext, $messagehtml, $attachment, $attachname,
        $usetrueaddress, $replyto, $replytoname, $wordwrapwidth);
    ');
}
