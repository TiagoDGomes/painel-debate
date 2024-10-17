<?php 

class Style {   
    public static function getCurrentPathStyle(){
        if (AccessCheck::isValidAdminPage()){
            return 'media/custom-admin';
        } else {
            return 'media/custom';
        }

    } 
    public static function getStyleURL($style, $admin=FALSE){
        $url =  "?i=" . $_GET['i'];
        if ($style){
            $url .=   "&s=" . $style;
        }
        if ($admin){
            $url .= "&g=" . $_GET['g'];
        }
        return $url;
    }
    


    public static function getCurrentStyle(){
        return @$_GET['s'];
    }
    public static function HTMLHeadCurrentStyle(){
        global $APP_VERSION;
        $s = Style::getCurrentStyle();

        if ($s){
            ?><link rel="stylesheet" href="<?= Style::getCurrentPathStyle() ?>/<?= $s ?>/<?= $s ?>.css?v=<?= @$APP_VERSION ?>"><?php
        } 
        if (!AccessCheck::isValidAdminPage() || !$s){
            ?>

            <link rel="stylesheet" href="media/default.css?v=<?= @$APP_VERSION ?>">
            <link rel="stylesheet" href="media/button.css?v=<?= @$APP_VERSION ?>">
            
            <?php
        }    
            
    }
}