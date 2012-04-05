<?php
ini_set('display_errors', 'On');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../../lib/bootstrap.php');

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();
$controller->setJob($_POST['url']);

header('Location: http://' . $_SERVER['HTTP_HOST']);