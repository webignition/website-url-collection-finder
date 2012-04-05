<?php

namespace webignition\WebsiteUrlCollectionFinder\UrlScopeComparer;

/**
 * Are two URLs in the same scope? 
 * 
 * Compare a source URL and a comparator.
 * 
 * Comparator is in the same scope as the source if:
 *  - scheme is the same or equivalent (e.g. http and https are equivlent)
 *  - hostname is the same or equivalent (equivalency looks at subdomain equivalence e.g. example.com and www.example.com
 *  - path is the same or greater (e.g. sourcepath = /one/two, comparatorpath = /one/two or /one/two/*
 * 
 * Comparison ignores:
 *  - port
 *  - user
 *  - pass
 *  - query
 *  - fragment
 * 
 * Refernce - url parts: * 
 *  scheme - e.g. http
 *  host
 *  port
 *  user
 *  pass
 *  path
 *  query - after the question mark ?
 *  fragment - after the hashmark #
 *
 * 
 * @package webignition\WebsiteUrlCollectionFinder\UrlScopeComparer
 *
 */
class UrlScopeComparer {
    
    /**
     *
     * @var string
     */
    private $sourceUrl = null;
    
    
    /**
     *
     * @var array 
     */
    private $sourceUrlParts = array();
    
    
    /**
     *
     * @var string
     */
    private $comparatorUrl = null;
    
    
    /**
     *
     * @var array
     */
    private $comparatorUrlParts = array();
    
    
    /**
     *
     * @var array
     */
    private $equivalentHosts = array();
    
    
    /**
     *
     * @var array
     */
    private $equivalentSchemes = array(
        'http' => 'https',
        'https' => 'http'
    );
    
    
    /**
     *
     * @param string $sourceUrl 
     */
    public function setSourceUrl($sourceUrl = '') {
        $this->sourceUrl = $sourceUrl;
        $this->sourceUrlParts = parse_url($this->sourceUrl);
    }
    
    
    public function isInScope($comparatorUrl) {       
        $this->comparatorUrl = $comparatorUrl;
        $this->comparatorUrlParts = parse_url($this->comparatorUrl);
        
        if ($this->areSourceAndComparatorUrlsIdentical()) {
            return true;
        }
        
        if ($this->isSourceUrlSubtringOfComparatorUrl()) {
            return true;
        }       
        
        if (!$this->arePartsIdentical('scheme')) {
            if (!$this->areSchemesEquivalent()) {
                return false;
            }
        }
        
        if (!$this->arePartsIdentical('host')) {
            if (!$this->areHostsEquivalent()) {
                return false;
            }
        }       
        
        
        return $this->isSourcePathSubtringOfComparatorPath();
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function isSourcePathSubtringOfComparatorPath() {
        if ($this->arePartsIdentical('path')) {
            return true;
        }
        
        if (!$this->hasSourcePart('path') && $this->hasComparatorPart('path')) {
            return true;
        }
        
        return substr($this->comparatorUrlParts['path'], 0, strlen($this->sourceUrlParts['path'])) == $this->sourceUrlParts['path'];
    }
    
    
    /**
     *
     * @return boolean
     */
    private function areSourceAndComparatorUrlsIdentical() {
        return $this->sourceUrl == $this->comparatorUrl;
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function isSourceUrlSubtringOfComparatorUrl() {
        return substr($this->comparatorUrl, 0, strlen($this->sourceUrl)) == $this->sourceUrl;       
    }
    
    
    /**
     *
     * @param string $host 
     */
    public function addEquivalentHost($host) {
        if (!in_array($host, $this->equivalentHosts)) {
            $this->equivalentHosts[] = $host;
        }
    }
   
    
    /**
     *
     * @param string $partName
     * @return boolean 
     */
    private function arePartsIdentical($partName) {
        if (!$this->areSourceAndComparatorPartsPresent($partName)) {
            return true;
        }
        
        if ($this->isOnlyOnePartSet($partName)) {
            return false;
        }       
        
        return $this->sourceUrlParts[$partName] == $this->comparatorUrlParts[$partName];
    }
    
    
    /**
     *
     * @return boolean 
     */    
    private function areSourceAndComparatorPartsPresent($partName) {
        return $this->hasSourcePart($partName) && $this->hasComparatorPart($partName);
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function isOnlyOnePartSet($partName) {
        if (!$this->hasSourcePart($partName) && $this->hasComparatorPart($partName)) {
            return true;
        }
        
        if ($this->hasSourcePart($partName) && !$this->hasComparatorPart($partName)) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function hasSourcePart($partName) {
        return $this->hasUrlPart($this->sourceUrlParts, $partName);
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function hasComparatorPart($partName) {
        return $this->hasUrlPart($this->comparatorUrlParts, $partName);
    } 
    
    
    /**
     *
     * @param string $partName
     * @return boolean 
     */
    private function hasUrlPart($urlParts, $partName) {
        if (!isset($urlParts[$partName])) {
            return false;
        }
        
        return $urlParts[$partName] != '';
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function areSchemesEquivalent() {
        if (!$this->areSourceAndComparatorPartsPresent('scheme')) {
            return false;
        }
        
        if ($this->equivalentSchemes[$this->sourceUrlParts['scheme']] == $this->comparatorUrlParts['scheme']) {
            return true;
        }
        
        if ($this->equivalentSchemes[$this->comparatorUrlParts['scheme']] == $this->sourceUrlParts['scheme']) {
            return true;
        }        
        
        return false;     
    }    
    

    /**
     *
     * @return boolean 
     */
    private function areHostsEquivalent() {
        if (!$this->areSourceAndComparatorPartsPresent('host')) {
            return false;
        }
        
        foreach ($this->equivalentHosts as $equivalentHost) {
            if ($this->comparatorUrlParts['host'] == $equivalentHost) {
                return true;
            }
        }
        
        return false;
    }
    
    
}