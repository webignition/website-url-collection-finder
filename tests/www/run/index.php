<?php
ini_set('display_errors', 'On');
ini_set('max_execution_time', 60);
require_once($_SERVER['DOCUMENT_ROOT'] . '/../../lib/bootstrap.php');

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();

$controller->queueRunner()->doNextBatch(1);
$controller->queueRunner()->persistQueues();

if (!isset($_GET['ajax'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST']);    
}