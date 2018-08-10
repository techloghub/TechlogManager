<?php
//执行对日志的检查（保障脚本工作正常）
$logs = array(
    "app_process/fullscan_runner.log" => 60, //分钟

);

function logCheck($msg) {
    file_put_contents("/tmp/check_log.log", "[".date("Y-m-d H:i:s")."] ".$msg."\n", FILE_APPEND);    
}

$emails = array(
	'zeyu203@qq.com'
);

function sendWarnMail($emails, $errors) {
    $mailTo = implode(",", $emails);
    $content = "";
    foreach ($errors as $error) {
        $content .= "{$error[0]} | {$error[1]}\n";
    }
    mail($mailTo, "LOG CHECK ERROR", $content);
}

//处理配置日志
function process($logs, $emails) {
    $logDir = __DIR__."/logs/";
    $now = time();
    $errors = array();
    foreach ($logs as $log => $interval) {
        logCheck("checking $log ...");
        $f = $logDir.$log;
        if (!file_exists($f)) {
            $errors[] = array($f, "file not exists!");
            continue;
        }
        $arr = stat($f);
        $diff = $now - $arr['mtime']; //修改时间的差量
        if ($diff > $interval * 60) {
            $errors[] = array($f, "file not modified in $diff minutes, may have error!");    
        }
    }
    if ($errors) {
        logCheck("all errors: \n".print_r($errors, true));
        sendWarnMail($emails, $errors);
    }

}
logCheck("start...");
process($logs, $emails);
logCheck("end!");
