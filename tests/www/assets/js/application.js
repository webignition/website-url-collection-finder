var application = {
    
    runQueue: function (callback) {
        $.get('/run/?ajax', function (data) {               
            $.get('/queue/new.php', function (newQueueItems) {
                $('#new-queue-count').text(newQueueItems.length);
                var newQueueList = $('#new-queue-list');
                newQueueList.html('');

                for (var newQueueItemIndex = 0; newQueueItemIndex < newQueueItems.length; newQueueItemIndex++) {
                    newQueueList.append('<li>'+newQueueItems[newQueueItemIndex]+'</li>');
                }                    

                $.get('/queue/processed.php', function (processedQueueItems) {
                    $('#processed-queue-count').text(processedQueueItems.length);
                    var processedQueueList = $('#processed-queue-list');
                    processedQueueList.html('');

                    for (var processedQueueItemIndex = 0; processedQueueItemIndex < processedQueueItems.length; processedQueueItemIndex++) {
                        processedQueueList.append('<li>'+processedQueueItems[processedQueueItemIndex]+'</li>');
                    } 
                    
                    callback();
                }, 'json');                    

            }, 'json');

        }, 'json');        
    }
    
};

application.config = {
};

var applicationController = function () {
    
    var initialise = function () {
        var runQueueButton = $('#form-run-queue');
        if (!runQueueButton) {
            return;
        }
        
        var queueProcessedCallback = function () {
            if ($('#new-queue-count').text() > 0) {
                application.runQueue(function () {
                    queueProcessedCallback();
                });
            }            
        };
        
        var automaticRunQueueButton = $('<input type="submit" id="automatic-run-queue" value="Run queue automatically" />').click(function (event) {
            event.preventDefault();           
            
            application.runQueue(function () {
                queueProcessedCallback();
            });            
        });
        
        runQueueButton.after(automaticRunQueueButton);
                
    };    
    
    this.initialise = initialise;
};


$(document).ready(function() {
    var app = new applicationController();
    app.initialise();
});