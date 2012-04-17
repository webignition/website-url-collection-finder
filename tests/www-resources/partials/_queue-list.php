<?php

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();

echo '<h2>Queue contents:</h2>';


$newQueueContents = $controller->queue('new')->contents();
echo '<h3>New (<span id="new-queue-count">'.count($newQueueContents).'</span>)</h3>';
if (count($newQueueContents)) {
    echo '<ul id="new-queue-list">';
    
    foreach ($newQueueContents as $queueItem) {
        echo '<li>'.$queueItem.'</li>';
    }    
    
    echo '</ul>';
} else {
    echo '<p>Job finished - hit cancel above to start again</p>'; 
}


$processedQueueContents = $controller->queue('processed')->contents();
echo '<h3>Processed (<span id="processed-queue-count">'.count($processedQueueContents).'</span>)</h3>';
echo '<ul id="processed-queue-list">';

foreach ($processedQueueContents as $queueItem) {
    echo '<li>'.$queueItem.'</li>';
}    

echo '</ul>';
