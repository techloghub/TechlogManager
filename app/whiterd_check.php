<?php

$emails = array(
	'zeyu203@qq.com'
);

function sendWarnMail($title, $content) {
    global $emails;
    $mailTo = implode(",", $emails);
    mail($mailTo, $title, $content);
}

function logCheck($msg) {
    file_put_contents("/tmp/check.log", "[".date("Y-m-d H:i:s")."] ".$msg."\n", FILE_APPEND);    
}

$dir = __DIR__."/../";
$server = php_uname("n");
logCheck("start...");
$cmd = "cd $dir; /usr/local/bin/php app/console -l 2>&1";
logCheck("cmd: $cmd");
exec($cmd, $out, $ret);
if ($ret !== 0) {
    $content = "cmd: ".$cmd."\n".implode("\n", $out);
    sendWarnMail("ERROR! app_console($server)", $content);
}
logCheck("end!");
