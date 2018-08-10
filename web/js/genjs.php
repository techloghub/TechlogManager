<?php
$files = glob("plugins/*");
//print_r($files);
foreach ($files as $file) {
    if (strpos($file, '.js') !== false) {
        $line = '<script type="text/javascript" src="/js/'.$file.'"></script>';
        echo $line."\n";
    }
}