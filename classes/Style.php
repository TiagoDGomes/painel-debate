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
        $url =  "?i=" . $_GET['i'] . "&s=" . $style;
        if ($admin){
            $url .= "&g=" . $_GET['g'];
        }
        return $url;
    }
    public static function HTMLMenu(){
        echo '<hr>';
        Style::HTMLMenuItems('media/custom', FALSE);
        if (AccessCheck::isValidAdminPage()){
            echo '<hr>';
            Style::HTMLMenuItems('media/custom-admin', TRUE);
        } 
    }
    public static function HTMLMenuItems($path, $admin=FALSE){
        global $HIDE_STYLES;
        $files = array_diff(scandir($path), array('..', '.'));
        foreach ($files as $file){
            if (in_array($file, $HIDE_STYLES)){
                // 
            } else {
                if (file_exists("$path/$file/$file.png")){
                    $style_in_line = "background-image: url($path/$file/$file.png);";
                } else if (file_exists("$path/$file/$file.svg")){
                    $style_in_line = "background-image: url($path/$file/$file.svg);";
                } else {
                    $style_in_line = '';
                }
                ?>
    
                <div class="item selector">
                    <a title="<?=$file?>" href="<?= Style::getStyleURL($file,$admin) ?>"><i style="<?= $style_in_line ?>" class="icon <?= $file ?>"></i></a>
                </div>
    
                <?php
            }
            
        }
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