<?php
ini_set('display_errors', 'On');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../../lib/bootstrap.php');

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();
$totals = array(
    'new' => $controller->queue('new')->length(),
    'processed' => $controller->queue('processed')->length(),
);

echo json_encode($totals);