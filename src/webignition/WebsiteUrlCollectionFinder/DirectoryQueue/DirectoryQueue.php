<?php

namespace webignition\WebsiteUrlCollectionFinder\DirectoryQueue;
use webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\DirectoryQueue
 *
 */
class DirectoryQueue extends Queue\Queue {       
    
    
    private $path = null;    
    
    public function initialise($path) {
        $this->path = $path;
    }
    
    
    /**
     *
     * @param string $url 
     */
    public function enqueue($url) {       
        if (!$this->contains($url)) {           
            file_put_contents($this->path . '/' . $this->normalise($url), $url);            
        }
    }
    
    public function dequeue() {
        $first = $this->getFirst();
        $this->removeFirst();
        
        return $first;
    }
    
    
    public function clear() {
        $directoryIterator = new \DirectoryIterator($this->path);
    
        foreach ($directoryIterator as $directoryItem) {            
            if (!$directoryItem->isDir()) {                
                unlink($directoryItem->getPathname());
            }
        }   
    }
    
    
    private function removeFirst() {
        foreach ($this->items() as $time => $filename) {
            return unlink($this->path . '/' . $filename);            
        }        
    }
       
    
    /**
     *
     * @return array
     */
    public function contents() {
        $items = $this->items();        
        $contents = array();
        
        foreach ($items as $filename) {
            $contents[] = $this->filenameToUrl($filename);
        }
        
        return $contents;
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
        
        foreach ($items as $time => $filename) {
            return $this->filenameToUrl($filename);
        }
    }
    
    
    /**
     *
     * @param string $filename
     * @return string
     */
    private function filenameToUrl($filename) {
        return file_get_contents($this->path . '/' . $filename);
    }
    
    
    /**
     *
     * @param string $url
     * @return string
     */
    public function contains($url) {
        return in_array($this->normalise($url), $this->items());
    }
    
    
    /**
     *
     * @return array
     */
    private function items() {        
        $directoryIterator = new \DirectoryIterator($this->path);
        
        $contents = array();        
        foreach ($directoryIterator as $directoryItem) {            
            if (!$directoryItem->isDir()) {
                if (!isset($contents[$directoryItem->getMTime()])) {
                    $contents[$directoryItem->getMTime()] = array();
                }
                
                $contents[$directoryItem->getMTime()][] = $directoryItem->getFilename();
            }
        }
        
        ksort($contents);
        
        $items = array();
        
        foreach ($contents as $dateSet) {
            foreach ($dateSet as $item) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    
    /**
     *
     * @param string $url
     * @return string
     */
    private function normalise($url) {
        return md5($url);
    }
    
}