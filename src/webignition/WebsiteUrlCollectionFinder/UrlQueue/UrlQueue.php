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
     * Index of items, for fast comparisons
     * 
     * @var array
     */
    protected $index = array();
    
    
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
        $this->index = array();
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
        $this->items[] = $url;
        $this->index[$url] = true;
    }
    
    /**
     *
     * @return string 
     */
    public function dequeue() {
        $first = array_shift($this->items);
        unset($this->index[$first]);
        return $first;
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
        return array_key_exists($url, $this->index);
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
            $this->buildIndex();
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
    
    
    private function buildIndex() {
        foreach ($this->items as $url) {
            $this->index[$url] = true;
        }
    }
    
}