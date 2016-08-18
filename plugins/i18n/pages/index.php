<?php
    $context = new rex_context([
        'page' => rex_be_controller::getCurrentPage(),
        'clang' => rex_clang::getCurrentId()
    ]);
    echo rex_view::clangSwitchAsButtons($context);

    $isSaved = GloebalsI18n::saveSettings();

    $content = '';
    $clang_code = rex_clang::getCurrent()->getCode();



    $field = 'strings';
    $name = $field . '_' . $clang_code;

    $value = GloebalsI18n::getSettings($name);
    $inputs = [];
    $inputs[] = [
        'label' => '<label for="' . GloebalsI18n::getFieldId($name) . '">' . GloebalsI18n::i18n($field) . '</label>',
        'field' => '<textarea class="form-control gloebals--code" id="' . GloebalsI18n::getFieldId($name) . '" name="' . GloebalsI18n::getFieldName($name) . '">' . $value . '</textarea>',
        'note' => GloebalsI18n::i18n($field . '_info')
    ];
    unset($id, $values);

    $fragment = new rex_fragment();
    $fragment->setVar('elements', $inputs, false);
    unset($inputs);

    $content .= '<fieldset><legend>' . GloebalsI18n::i18n('gloebals_settings_for_in_clang', GloebalsI18n::i18n($field), strtoupper($clang_code)) . '</legend>';
    $content .= $fragment->parse('core/form/form.php');
    unset($fragment, $setting);


    $inputs = [];
    $inputs[] = [
        'field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="' . GloebalsI18n::i18n('save') . '">' . GloebalsI18n::i18n('save') . '</button>'
    ];
    $inputs[] = [
        'field' => '<button class="btn btn-reset" type="reset" name="btn_reset" value="' . GloebalsI18n::i18n('reset') . '" data-confirm="' . GloebalsI18n::i18n('reset_info') . '">' . GloebalsI18n::i18n('reset') . '</button>'
    ];
    $inputs[] = [
        'field' => '<input type="hidden" name="clang" value="' . rex_clang::getCurrentId() . '" />'
    ];

    $fragment = new rex_fragment();
    $fragment->setVar('flush', true);
    $fragment->setVar('elements', $inputs, false);
    $buttons = $fragment->parse('core/form/submit.php');
    unset($inputs, $fragment);

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('body', $content, false);
    $fragment->setVar('buttons', $buttons, false);
    $content = $fragment->parse('core/page/section.php');
    unset($fragment, $buttons);

?><form action="<?php echo rex_url::currentBackendPage();?>" method="post">
    <?php if(GloebalsI18n::hasMessages()) echo rex_view::success(GloebalsI18n::getMessage()); ?>
    <?php if(GloebalsI18n::hasErrors()) echo rex_view::success(GloebalsI18n::getError()); ?>
    <?php echo $content;?>
</form>
