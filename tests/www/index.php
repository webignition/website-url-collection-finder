<?php
ini_set('display_errors', 'On');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../../lib/bootstrap.php');

$controller = new \webignition\WebsiteUrlCollectionFinder\Test\Controller();
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>        
        <link href="/assets/css/style.css" type="text/css" rel="stylesheet" media="screen, print, projection" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/application.js"></script>
    </head>
    
    <body>
        <div id="wrap">
            <section id="header">    
            </section>

            <section id="content">
                <div id="new">
                    <?php // ?>
                </div>
                
                <div id="queue-runner">
                    <?php
                        if ($controller->hasJob()) {
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/../www-resources/partials/_job-summary.php');
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/../www-resources/partials/_run-queue-form.php');
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/../www-resources/partials/_queue-list.php');                             
                            
                        } else {
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/../www-resources/partials/_new-job.php');
                        }
                    ?>
                </div>
                    
            </section>

            <section id="footer">
            </section>    
        </div>
    </body>
</html>