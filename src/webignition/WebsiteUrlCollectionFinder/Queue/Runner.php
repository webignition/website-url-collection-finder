<?php

namespace webignition\WebsiteUrlCollectionFinder\Queue;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\Queue
 *
 */
class Runner {
    
    /**
     *
     * @var webignition\WebsiteUrlCollectionFinder\Queue\Queue
     */
    private $newQueue = null;
    
    /**
     *
     * @var webignition\WebsiteUrlCollectionFinder\Queue\Queue
     */
    private $processedQueue = null;
    
    
    /**
     *
     * @var string
     */
    private $jobUrl = null;
    
    
    /**
     *
     * @var 
     */
    private $urlScopeComparer = null;
    
    
    /**
     * Collection of method names for which queue states should be persisted
     * each time the method is called
     * 
     * Persistence of queues is expensive, performance is improved if we
     * only persist when needed
     * 
     * @var array
     */
    private $persistOn = array();    
    
    
    /**
     *
     * @param webignition\WebsiteUrlCollectionFinder\Queue\Queue $newQueue 
     */
    public function setNewQueue(\webignition\WebsiteUrlCollectionFinder\Queue\Queue $newQueue) {
        $this->newQueue = $newQueue;
    }
    
    
    /**
     *
     * @param webignition\WebsiteUrlCollectionFinder\Queue\Queue $processedQueue 
     */
    public function setProcessedQueue(\webignition\WebsiteUrlCollectionFinder\Queue\Queue $processedQueue) {
        $this->processedQueue = $processedQueue;
    }
    
    /**
     *
     * @param string $methodName 
     */
    public function enablePersistOn($methodName) {        
        $this->persistOn[$methodName] = true;

    }
    
    /**
     *
     * @param string $methodName 
     */
    public function disablePersistOn($methodName) {
        unset($this->persistOn[$methodName]);
    }    
    
    
    /**
     *
     * @param string $jobUrl 
     */
    public function setJobUrl($jobUrl) {
        $this->jobUrl = $jobUrl;
    }

    
    public function doNext() {         
        if (!$this->hasNewQueue() || !$this->hasProcessedQueue()) {
            return null;
        }

        $nextUrl = $this->newQueue->dequeue();
        
        set_exception_handler(function ($exception) {
            var_dump($exception);
            exit();
        });        
        
        $linkFinder = new \webignition\WebDocumentLinkUrlFinder\WebDocumentLinkUrlFinder();
        $linkFinder->setSourceUrl($nextUrl);
        $urls = $linkFinder->urls(); 

        restore_exception_handler();               
        
        $this->processedQueue->enqueue($nextUrl);        
        
        foreach ($urls as $url) {            
            if ($this->urlScopeComparer()->isInScope($url)) {
                if (!$this->processedQueue->contains($url) && !$this->newQueue->contains($url)) {
                    $this->newQueue->enqueue($url);
                }
            }
        }
        
        if ($this->shouldPersistOn(__FUNCTION__)) {
            $this->newQueue->save();
            $this->processedQueue->save();
        }       
    }    
    
    
    public function doNextBatch($batchSize = 1) {        
        $batchCount = 0;
        while (($batchCount < $batchSize) && $this->newQueue->length() > 0) {
            $this->doNext();
            $batchCount++;
        }        
        
        if ($this->shouldPersistOn(__FUNCTION__)) {
            $this->newQueue->save();
            $this->processedQueue->save();
            $this->newQueue->reset();
            $this->processedQueue->reset();               
        }    
    }
    
    
    public function persistQueues() {
        $this->newQueue->save();
        $this->processedQueue->save();          
    }
    
    
    /**
     *
     * @param string $methodName
     * @return boolean 
     */
    private function shouldPersistOn($methodName) {        
        return isset($this->persistOn[$methodName]);
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function hasNewQueue() {
        return $this->newQueue instanceof \webignition\WebsiteUrlCollectionFinder\Queue\Queue;
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasProcessedQueue() {
        return $this->processedQueue instanceof \webignition\WebsiteUrlCollectionFinder\Queue\Queue;
    }
    
    
    /**
     *
     * @return \webignition\WebsiteUrlCollectionFinder\UrlScopeComparer\UrlScopeComparer
     */
    private function urlScopeComparer() {
        if (is_null($this->urlScopeComparer)) {
            $this->urlScopeComparer = new \webignition\WebsiteUrlCollectionFinder\UrlScopeComparer\UrlScopeComparer();
            $this->urlScopeComparer->setSourceUrl($this->jobUrl);
            $this->setDefaultUrlScopeComparerEquivalentHosts();
        }        
       
        return $this->urlScopeComparer;
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function jobUrlHost() {
        return parse_url($this->jobUrl, PHP_URL_HOST);
    }
    
    
    
    private function setDefaultUrlScopeComparerEquivalentHosts() {
        $jobUrlHost = $this->jobUrlHost();
        if (substr($jobUrlHost, 0, strlen('www.')) != 'www.') {
            $this->urlScopeComparer->addEquivalentHost('www.'.$jobUrlHost);
        }
    }
    
}