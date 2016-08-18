<?php
    rex_extension::register('PACKAGES_INCLUDED', function ($params) {

        GloebalsFields::addFields();

    }, rex_extension::EARLY);
?>
