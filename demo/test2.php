<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);

$text =
iconv("UTF-8", "ISO-8859-1//IGNORE", $text);
