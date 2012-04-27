<?php

namespace webignition\WebsiteUrlCollectionFinder\FileQueue;
use webignition\WebsiteUrlCollectionFinder\UrlQueue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\FileQueue
 *
 */
class FileQueue extends UrlQueue\UrlQueue {    
 
    
    /**
     *
     * @var string
     */
    private $path = null;
    
    
    /**
     *
     * @param string $path 
     */
    public function initialise($path) {
        $this->path = $path;
        if (!file_exists($this->path)) {
            file_put_contents($this->path, '');
        }       
        
        
        $this->items();
    }
    
    public function save() { 
        if (!$this->hasItems()) {
            file_put_contents($this->path, '');
            return;            
        }
        
        $contents = implode("\n", $this->contents());
        
        $fileHandle = fopen($this->path, 'w');
        fwrite($fileHandle, $contents);
        fclose($fileHandle);
    }


    
    protected function load() {
        $this->items = array();
        
        if (filesize($this->path) > 0) {
            $fileHandle = fopen($this->path, 'r');    
            $contents = explode("\n", fread($fileHandle, filesize($this->path)));
            fclose($fileHandle);
            
            foreach ($contents as $url) {
                $url = trim($url);
                $this->enqueue($url);
            }
        }
    } 

    

}