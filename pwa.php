<?php 
header('Content-Type: application/json'); 
include_once('core.php');
?>{
  "background_color": "green",
  "description": "Painel de debates.",
  "display": "fullscreen",
  "icons": [
    {
      "src": "icon/fox-icon.png",
      "sizes": "192x192",
      "type": "image/png"
    }
  ],
  "name": "Painel de debates",
  "short_name": "Painel",
  "start_url": "<?= str_replace ('pwa.php','', $url_file) . $url_base ?>"
}