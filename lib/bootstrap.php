<?php
namespace webignition\WebsiteUrlCollectionFinder;

function autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {        
        $file = sprintf(
            '%s/%s.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );        
        
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

autoload( __DIR__ . '/../src');
autoload( __DIR__ . '/../vendor/webignition/absolute-url-deriver/src');
autoload( __DIR__ . '/../vendor/webignition/http-client/src');
autoload( __DIR__ . '/../vendor/webignition/html-document-link-url-finder/src');
autoload( __DIR__ . '/../vendor/webignition/web-document-link-url-finder/src');