<?php

namespace webignition\WebsiteUrlCollectionFinder\Test;

/**
 * 
 * @package webignition\WebsiteUrlCollectionFinder\Test
 *
 */
class Controller {
    
    const QUEUE_TYPE_DIRECTORY = 'directory';
    const QUEUE_TYPE_FILE = 'file';
    
    const DATA_RELATIVE_PATH = '/website-url-collection-finder';
    const JOB_FILENAME = 'job';
    
    const NEW_QUEUE_NAME = 'new';
    const PROCESSED_QUEUE_NAME = 'processed';
    
    private $queues = array();
    
    /**
     *
     * @var webignition\WebsiteUrlCollectionFinder\Queue\Runner
     */
    private $queueRunner = null;
    
    
    private $job = null;
    
    private $queueNames = array(
        self::NEW_QUEUE_NAME,
        self::PROCESSED_QUEUE_NAME
    );
    
    private $queueType = self::QUEUE_TYPE_FILE;    
    
    
    public function __construct() {
        foreach ($this->queueNames as $queueName) {
            $this->initialiseQueue($queueName);
        }
    }
    
    
    /**
     *
     * @param string $queueName 
     */
    public function initialiseQueue($queueName) {        
        if ($this->queueType == self::QUEUE_TYPE_DIRECTORY) {
            $this->queues[$queueName] = new \webignition\WebsiteUrlCollectionFinder\DirectoryQueue\DirectoryQueue();
            $this->queues[$queueName]->initialise($this->directoryQueuePath($queueName));              
        }
        
        if ($this->queueType == self::QUEUE_TYPE_FILE) {
            $this->queues[$queueName] = new \webignition\WebsiteUrlCollectionFinder\FileQueue\FileQueue();
            $this->queues[$queueName]->initialise($this->fileQueuePath($queueName));              
        }
    }
    
    
    /**
     *
     * @return stdClass
     */
    public function job() {
        if (is_null($this->job)) {
            if ($this->hasJob()) {
                $this->job = json_decode(file_get_contents($this->jobPath()));
            }
        }
        
        return $this->job;        
    }
    
    
    /**
     *
     * @param string $queueName
     * @return \webignition\WebsiteUrlCollectionFinder\DirectoryQueue\DirectoryQueue 
     */
    public function queue($queueName) {
        return $this->queues[$queueName];
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasJob() {
        return file_exists($this->jobPath());
    }
    
    
    /**
     *
     * @param string $url 
     */
    public function setJob($url) {        
        foreach ($this->queues as $queue) {
            $queue->clear();
        }   
        
        $this->job = new \stdClass();
        $this->job->url = $url;
        $this->job->status = 'in-progress';        
        
        $this->saveJob();
        
        $this->queues[self::NEW_QUEUE_NAME]->enqueue($url);
    }
    
    
    public function cancelJob() {
        foreach ($this->queues as $queue) {
            $queue->clear();
        }
        
        unlink($this->jobPath());
    }

    
    /**
     *
     * @return string
     */
    private function dataPath() {
        return sys_get_temp_dir() . self::DATA_RELATIVE_PATH;
    }
    
    
    /**
     *
     * @param string $queueName
     * @return string 
     */
    private function directoryQueuePath($queueName) {
        $queuePath = $this->dataPath() . '/' . $queueName;
        
        if (!file_exists($queuePath)) {
            mkdir($queuePath, 0777, true);
        }
        
        return $queuePath;        
    }
    
    
    /**
     *
     * @param string $queueName
     * @return string 
     */
    private function fileQueuePath($queueName) {        
        if (!file_exists($this->dataPath())) {
            mkdir($this->dataPath(), 0777, true);
        }
        
        return $this->dataPath() . '/' . $queueName;        
    }    

    
    /**
     *
     * @return string
     */
    private function jobPath() {
        return $this->dataPath() . '/' . self::JOB_FILENAME;
    }
    
    private function saveJob() {      
        file_put_contents($this->jobPath(), json_encode($this->job));
    }  
    
    
    /**
     *
     * @return webignition\WebsiteUrlCollectionFinder\Queue\Runner 
     */
    public function queueRunner() {
        if (is_null($this->queueRunner)) {
            $this->queueRunner = new \webignition\WebsiteUrlCollectionFinder\Queue\Runner();
            $this->queueRunner->setNewQueue($this->queues[self::NEW_QUEUE_NAME]);
            $this->queueRunner->setProcessedQueue($this->queues[self::PROCESSED_QUEUE_NAME]);
            $this->queueRunner->setJobUrl($this->job()->url);
        }
        
        return $this->queueRunner;
    }
}