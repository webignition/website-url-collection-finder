var application = {
    
    runQueue: function (callback) {
        $.get('/run/?ajax', function (data) {               
            $.get('/queue/totals.php', function (totals) {                
                $('#new-queue-count').text(totals['new']);
                
                var newQueueList = $('#new-queue-list');
                newQueueList.html(''); 
                
                $('#processed-queue-count').text(totals['processed']);
                var processedQueueList = $('#processed-queue-list');
                processedQueueList.html('');
                
                callback();
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