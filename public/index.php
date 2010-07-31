<?php
require '../application/config.php';

use rpo\mvc\Application;
use rpo\http\HTTPRequest;

$application = new Application();
$application->attach( new com\base\Imns() );

$application->handle( HTTPRequest::getInstance( '/imns/' ) );