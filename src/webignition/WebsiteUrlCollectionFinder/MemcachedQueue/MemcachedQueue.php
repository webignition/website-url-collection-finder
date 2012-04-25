<?php

namespace webignition\WebsiteUrlCollectionFinder\MemcachedQueue;
use webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\FileQueue
 *
 */
class MemcachedQueue extends Queue\Queue {    
 
    
    /**
     *
     * @var \Memcached 
     */
    private $memcached = null;
    
    
    /**
     *
     * 
     * @var string
     */
    private $key = null;
    
    
    /**
     *
     * @var array
     */
    private $items = null;
    
    
    /**
     *
     * @param string $path 
     */
    public function initialise(\Memcached $memcached, $key) {
        $this->memcached = $memcached;   
        $this->key = $key;
        $this->items();
    }
    
    
    
    public function reset() {
        $this->items = null;
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
    
    
    public function save() {
        $this->memcached->set($this->key, $this->items);
    }
    
    
    /**
     *
     * @return int
     */
    public function length() {
        return count($this->items());
    }
    
    
    public function clear() {        
        $this->reset();
        $this->save();
    }
       
    
    /**
     *
     * @return array
     */
    public function contents() {        
        return $this->items();
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
     * @param string $url
     * @return string
     */
    public function contains($url) {
        return in_array($url, $this->items());
    }
    
    
    /**
     *
     * @return array
     */
    private function items() { 
        if (!$this->hasItems()) {
            $this->items = $this->memcached->get($this->key);
            if (!$this->items) {
                $this->items = array();
            }
                
        }
        
        return $this->items;
    }
    
    
    /**
     *
     * @return boolean
     */
    private function hasItems() {
        return $this->items !== null;
    }    

}