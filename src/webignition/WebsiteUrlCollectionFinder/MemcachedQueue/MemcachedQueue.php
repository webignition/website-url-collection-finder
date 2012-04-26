<?php

namespace webignition\WebsiteUrlCollectionFinder\MemcachedQueue;
use webignition\WebsiteUrlCollectionFinder\UrlQueue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\FileQueue
 *
 */
class MemcachedQueue extends UrlQueue\UrlQueue {    
 
    
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
     * @param string $path 
     */
    public function initialise(\Memcached $memcached, $key) {
        $this->memcached = $memcached;   
        $this->key = $key;
        $this->items();
    }
    
    public function save() {
        $this->memcached->set($this->key, $this->serialize());        
    }    
    
    protected function load() { 
        $this->unserialize($this->memcached->get($this->key));      
    }
    
    
    public function serialize() {
        return implode("\n", $this->items);
    }
    
    public function unserialize($string) {
        $this->items =  explode("\n", trim($string));
    }
  

}