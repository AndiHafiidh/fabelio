<body>    
    <div class="preloader">
        <img class="col mb-4" src="<? echo base_url('assets/img/logo.svg'); ?>" alt="backgroung.svg" height="50px">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>            
        </div>    
    </div>
    <?php
        require_once('content.php');               
        require_once('js.php');
        require_once('script.php');
    ?>        
    </body>
</html>