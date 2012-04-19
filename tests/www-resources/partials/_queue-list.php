<?php

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();

echo '<h2>Queue contents:</h2>';


$newQueueContents = $controller->queue('new')->contents();
echo '<div class="queue queue-new">';
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
echo '</div>';


$processedQueueContents = $controller->queue('processed')->contents();
echo '<div class="queue queue-processed">';
echo '<h3>Processed (<span id="processed-queue-count">'.count($processedQueueContents).'</span>)</h3>';
echo '<ul id="processed-queue-list">';

foreach ($processedQueueContents as $queueItem) {
    echo '<li>'.$queueItem.'</li>';
}    

echo '</ul>';
echo '</div>';