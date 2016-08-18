<?php
    $context = new rex_context([
        'page' => rex_be_controller::getCurrentPage(),
        'clang' => rex_clang::getCurrentId()
    ]);
    echo rex_view::clangSwitchAsButtons($context);

    $isSaved = GloebalsFields::saveSettings();

    $content = '';
    $clang_code = rex_clang::getCurrent()->getCode();



    $field = 'fields';
    $name = $field . '_' . $clang_code;

    $value = GloebalsFields::getSettings($name);
    $inputs = [];
    $inputs[] = [
        'label' => '<label for="' . GloebalsFields::getFieldId($name) . '">' . GloebalsFields::i18n($field) . '</label>',
        'field' => '<textarea class="form-control gloebals--code" id="' . GloebalsFields::getFieldId($name) . '" name="' . GloebalsFields::getFieldName($name) . '">' . $value . '</textarea>',
        'note' => GloebalsFields::i18n($field . '_info')
    ];
    unset($id, $values);

    $fragment = new rex_fragment();
    $fragment->setVar('elements', $inputs, false);
    unset($inputs);

    $content .= '<fieldset><legend>' . GloebalsFields::i18n('gloebals_settings_for_in_clang', GloebalsFields::i18n($field), strtoupper($clang_code)) . '</legend>';
    $content .= $fragment->parse('core/form/form.php');
    unset($fragment, $setting);


    $inputs = [];
    $inputs[] = [
        'field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="' . GloebalsFields::i18n('save') . '">' . GloebalsFields::i18n('save') . '</button>'
    ];
    $inputs[] = [
        'field' => '<button class="btn btn-reset" type="reset" name="btn_reset" value="' . GloebalsFields::i18n('reset') . '" data-confirm="' . GloebalsFields::i18n('reset_info') . '">' . GloebalsFields::i18n('reset') . '</button>'
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
    <?php if(GloebalsFields::hasMessages()) echo rex_view::success(GloebalsFields::getMessage()); ?>
    <?php if(GloebalsFields::hasErrors()) echo rex_view::success(GloebalsFields::getError()); ?>
    <?php echo $content;?>
</form>
