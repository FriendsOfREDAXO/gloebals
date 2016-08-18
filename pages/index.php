<?php

echo rex_view::title($this->i18n('title'));

if($subpage = rex_be_controller::getCurrentPagePart(2))
{
    rex_be_controller::includeCurrentPageSubPath();
}
else
{
    echo '<p>' . $this->i18n('gloebals_no_plugins_installed') . '</p>';
}
