<?php

namespace webignition\WebsiteUrlCollectionFinder\UrlQueue;
use webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\UrlQueue
 *
 */
abstract class UrlQueue implements Queue\Queue {    
    
    /**
     * Collection of URLs in queue
     * 
     * @var array
     */
    protected $items = null;
    
    
    /**
     * Populate $this->items
     *  
     */
    abstract protected function load();
    

    /**
     * Persist $this->items
     *  
     */
    abstract protected function save();    
    
    
    
    public function reset() {
        $this->items = null;
    }
    
    
    public function clear() {        
        $this->reset();
        $this->save();
    }    
    
    
    /**
     *
     * @param string $url 
     */
    public function enqueue($url) {     
        if (!$this->contains($url)) {
            array_push($this->items, $url);
        }
    }
    
    /**
     *
     * @return string 
     */
    public function dequeue() {
        return array_shift($this->items);
    }    
    
    
    /**
     *
     * @return array
     */
    public function contents() {        
        return $this->items();
    }
    
    
    /**
     *
     * @return int
     */
    public function length() {
        return count($this->items());
    }    
    
    
    /**
     *
     * @param string $url
     * @return string
     */
    public function contains($url) {
        return in_array($url, $this->items());
    }
    
    
    /**
     * Get first URL from queue
     * 
     * @return string
     */
    public function getFirst() {        
        $items = $this->items();
        return array_shift($items);
    }
    
    
    /**
     *
     * @return array
     */
    protected function items() { 
        if (!$this->hasItems()) {
            $this->load();
        }
        
        return $this->items;
    }
    
    
    /**
     *
     * @return boolean
     */
    protected function hasItems() {
        return $this->items !== null;
    }    
    
}