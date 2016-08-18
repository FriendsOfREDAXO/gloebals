<?php
    if (rex::isBackend() && rex::getUser() && rex_be_controller::getCurrentPagePart(1) == 'gloebal')
    {
        rex_view::addCssFile($this->getAssetsUrl('css/be.css'));
        rex_view::addJsFile($this->getAssetsUrl('js/be.js'));
    }
?>
