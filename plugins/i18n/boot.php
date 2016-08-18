<?php
    rex_extension::register('PACKAGES_INCLUDED', function ($params) {

        GloebalsI18n::addTranslations();

    }, rex_extension::EARLY);
?>
