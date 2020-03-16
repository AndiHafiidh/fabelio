<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php if (isset($title)){ echo $title; }else{ echo "Fabelio Test Andi"; }?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">        
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url('assets/img/128x128px.png'); ?>">

        <?php 
            require_once('css.php'); 
        ?>

    </head>
    