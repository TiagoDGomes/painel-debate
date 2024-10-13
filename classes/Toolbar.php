<?php
class Toolbar {

    public static function HTMLMenu(){
        if (AccessCheck::isValidAdminPage()){
            Toolbar::HTMLLinkAnchor('javascript:edit()','Editar','edit','');
            Toolbar::HTMLLinkAnchor('javascript:save()','Salvar','save','');  
            echo '<hr>';    
            Toolbar::HTMLLinkAnchor(Style::getStyleURL('',TRUE),'','','');        
            Toolbar::HTMLMenuItems('media/custom-admin', TRUE);
        } 
        echo '<hr>';
        Toolbar::HTMLMenuItems('media/custom', FALSE);
    }

    public static function HTMLLinkAnchor($url, $title, $class, $style_in_line){
        ?>

            <div class="item <?= $class ?>">
                <a title="<?= $title ?>" href="<?= $url ?>">
                    <i style="<?= $style_in_line ?>" class="icon <?= $class ?>"></i>
                </a>
            </div>
            
        <?php
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
                $url = Style::getStyleURL($file,$admin);
                Toolbar::HTMLLinkAnchor($url,$file,$file, $style_in_line);                
            }            
        }
    }
}