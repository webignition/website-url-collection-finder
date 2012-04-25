<?php

namespace webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\Queue
 *
 */
interface Queue {    
   
    
    /**
     *
     * @param mixed $item 
     */
    public function enqueue($item);
    
    
    /**
     *
     * @return string 
     */
    public function dequeue();
    
}