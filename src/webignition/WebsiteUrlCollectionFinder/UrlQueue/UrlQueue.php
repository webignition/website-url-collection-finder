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
     *
     * @var int
     */
    private $length = 0;
    
    
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
        $this->items[$url] = true;
        $this->length++;
    }
    
    /**
     *
     * @return string 
     */
    public function dequeue() {
        reset($this->items);
        $first = key($this->items);        
        unset($this->items[$first]);
        $this->length--;
        return $first;
    }    
    
    
    /**
     *
     * @return array
     */
    public function contents() {        
        return array_keys($this->items());
    }
    
    
    /**
     *
     * @return int
     */
    public function length() {
        return $this->length;
    }    
    
    
    /**
     *
     * @param string $url
     * @return string
     */
    public function contains($url) {        
        return array_key_exists($url, $this->items);
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
        return $this->length > 0;
    } 
    
}