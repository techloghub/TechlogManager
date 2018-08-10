@ECHO OFF
SET BIN_TARGET=%~dp0\"../chrisboulton/php-resque/bin"\resque
php "%BIN_TARGET%" %*
