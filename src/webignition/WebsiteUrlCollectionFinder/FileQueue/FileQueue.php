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
    }

    
    
    public function save() {        
        if (is_null($this->items)) {
            file_put_contents($this->path, '');
            return;
        }
        
        $contents = implode("\n", $this->items);        
        if ($contents != '') {
            $contents .= "\n";
        }
        
        $fileHandle = fopen($this->path, 'w');
        fwrite($fileHandle, $contents);
        fclose($fileHandle);
    }


    
    protected function load() {
        $this->items = array();
        
        if (filesize($this->path) > 0) {
            $fileHandle = fopen($this->path, 'r');    
            $contents = trim(fread($fileHandle, filesize($this->path)));                
            $this->items = explode("\n", $contents);
            fclose($fileHandle);                 
        }        
    }

    

}