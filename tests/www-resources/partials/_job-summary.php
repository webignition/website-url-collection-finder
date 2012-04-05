<div class="job-summary">
    <h2>Current job: <?php echo $controller->job()->url; ?></h2>

    <form id="form-cancel" method="post" action="/cancel/">
        <input type="submit" value="Cancel" />
    </form>    
</div>