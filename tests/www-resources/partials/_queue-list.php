<?php

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();

echo '<h2>Queue contents:</h2>';


$newQueueContents = $controller->queue('new')->contents();
echo '<h3>New ('.count($newQueueContents).')</h3>';
if (count($newQueueContents)) {
    echo '<ul>';
    
    foreach ($newQueueContents as $queueItem) {
        echo '<li>'.$queueItem.'</li>';
    }    
    
    echo '</ul>';
} else {
    echo '<p>Job finished - hit cancel above to start again</p>'; 
}


$processedQueueContents = $controller->queue('processed')->contents();
echo '<h3>Processed ('.count($processedQueueContents).')</h3>';
if (count($processedQueueContents)) {
    echo '<ul>';
    
    foreach ($processedQueueContents as $queueItem) {
        echo '<li>'.$queueItem.'</li>';
    }    
    
    echo '</ul>';
}