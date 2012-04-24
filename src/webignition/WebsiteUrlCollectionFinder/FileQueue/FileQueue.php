<?php

namespace webignition\WebsiteUrlCollectionFinder\FileQueue;
use webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\FileQueue
 *
 */
class FileQueue extends Queue\Queue {
    
 
    
    /**
     *
     * @var string
     */
    private $path = null;
    
    
    /**
     *
     * @var array
     */
    private $items = null;
    
    
    /**
     *
     * @param string $path 
     */
    public function initialise($path) {
        $this->path = $path;
        if (!file_exists($this->path)) {
            file_put_contents($this->path, '');
        }
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
            $fileHandle = fopen($this->path, 'a');
            fwrite($fileHandle, $url."\n");
            fclose($fileHandle);         
        }
    }
    
    public function dequeue() {
        $first = $this->getFirst();
        $this->removeFirst();
        
        return $first;
    }
    
    
    public function clear() {
        file_put_contents($this->path, '');
        $this->reset();
    }
    
    
    private function removeFirst() {
        $items = $this->items();
        array_shift($items);
 
        $contents = implode("\n", $items);
        if ($contents != '') {
            $contents .= "\n";
        }
        
        $fileHandle = fopen($this->path, 'w');
        fwrite($fileHandle, $contents);
        fclose($fileHandle);
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
        if (!$this->hasItems()) {
            $fileHandle = fopen($this->path, 'r');
            $firstLine = $this->readNextLine($fileHandle);  
            fclose($fileHandle);
            return $firstLine;            
        }
        
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
            $this->items = array();
            
            $fileHandle = fopen($this->path, 'r');
            while (($nextLine = $this->readNextLine($fileHandle)) != null) {
                $this->items[] = $nextLine;
            }

            fclose($fileHandle);           
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
    
    
    /**
     *
     * @param resource $fileHandle
     * @return string 
     */
    private function readNextLine($fileHandle) {
        $currentByte = '';
        $currentContents = null;
        while (($currentByte = fread($fileHandle, 1)) != "\n" && !feof($fileHandle)) {
            $currentContents .= $currentByte;
        }
        
        return $currentContents;
    }

    

}