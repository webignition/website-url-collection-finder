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
        $this->memcached->set($this->key, $this->items);
    }    
    
    protected function load() {
        $this->items = $this->memcached->get($this->key);
        if (!$this->items) {
            $this->items = array();
        }
    }
  

}