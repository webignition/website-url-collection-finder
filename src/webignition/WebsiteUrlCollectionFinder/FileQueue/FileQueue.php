<?php

namespace webignition\WebsiteUrlCollectionFinder\FileQueue;
use webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\FileQueue
 *
 */
class FileQueue extends Queue\Queue {       
    
    
    private $path = null;    
    
    public function initialise($path) {
        $this->path = $path;
        if (!file_exists($this->path)) {
            file_put_contents($this->path, '');
        }
    }
    
    
    /**
     *
     * @param string $url 
     */
    public function enqueue($url) {        
        if (!$this->contains($url)) {
            file_put_contents($this->path, $url."\n", FILE_APPEND);           
        }
    }
    
    public function dequeue() {
        $first = $this->getFirst();
        $this->removeFirst();
        
        return $first;
    }
    
    
    public function clear() {
        file_put_contents($this->path, ''); 
    }
    
    
    private function removeFirst() {
        $newItems = array_slice($this->items(), 1);
        $contents = implode("\n", $newItems);
        if ($contents != '') {
            $contents .= "\n";
        }       
        
        file_put_contents($this->path, $contents);
    }
       
    
    /**
     *
     * @return array
     */
    public function contents() {        
        return file($this->path);
    }
    
    
    /**
     * Get first URL from queue
     * 
     * @return string
     */
    public function getFirst() {
        $items = $this->items();
        if (!count($items)) {
            return null;
        }
        
        return $items[0];
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
        $items = file($this->path);
        array_walk($items, function($val,$key) use(&$items){ 
            $items[$key] = trim($items[$key]);
        });        
        
        return $items;
    }    
}