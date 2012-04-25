<?php

namespace webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\Queue
 *
 */
abstract class Queue {    
    
    
    /**
     *
     * @param string $url 
     */
    abstract public function enqueue($url);
    
    
    /**
     *
     * @return string 
     */
    abstract public function dequeue();
    
    
    /**
     * 
     * @param string $url 
     */
    abstract public function contains($url);
    
}