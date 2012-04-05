<?php
ini_set('display_errors', 'On');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../../lib/bootstrap.php');

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();
$newQueueContents = $controller->queue('new')->contents();

echo json_encode($newQueueContents);