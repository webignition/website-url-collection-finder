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
     * @var \webignition\Http\Client\CachingClient 
     */
    private $httpClient = null;
    
    
    /**
     *
     * @var 
     */
    private $urlScopeComparer = null;
    
    
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
     * @param string $jobUrl 
     */
    public function setJobUrl($jobUrl) {
        $this->jobUrl = $jobUrl;
    }
    
    
    
    
    
    
    public function doNext() {
        if (!$this->hasNewQueue() || !$this->hasProcessedQueue()) {
            return null;
        }
        
        $nextUrl = $this->newQueue->dequeue(); // 1        
        
        $request = new \HttpRequest($nextUrl);       
        $response = $this->httpClient()->getResponse($request); // 2
        
        $this->processedQueue->enqueue($nextUrl); // 3
        
        $linkFinder = new \webignition\HtmlDocumentLinkUrlFinder\HtmlDocumentLinkUrlFinder();
        $linkFinder->setSourceContent($response->getBody());
        $linkFinder->setSourceUrl($request->getUrl());
        
        $urls = $linkFinder->urls();
        
        foreach ($urls as $url) {            
            if ($this->urlScopeComparer()->isInScope($url)) {
                if (!$this->processedQueue->contains($url) && !$this->newQueue->contains($url)) {
                    $this->newQueue->enqueue($url);
                }
            }
        }
    }
    
    
    /**
     *
     * @return webignition\Http\Client\Client
     */
    private function httpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new \webignition\Http\Client\CachingClient();
            $this->httpClient->redirectHandler()->enable();
        }
        
        return $this->httpClient;
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