<?php $glpiticket_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_1_supported == 1; ?>

<?php if ($glpiticket_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
<?php return;
endif; ?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Settings') ?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php')); ?>
<?php endif; ?>

<form action="" method="post" ng-non-bindable>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'App Token'); ?></label>
        <input type="text" class="form-control" name="app_token" value="<?php echo isset($data['app_token']) ? htmlspecialchars($data['app_token']) : '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'User Token'); ?></label>
        <input type="text" class="form-control" name="user_token" value="<?php echo isset($data['user_token']) ? htmlspecialchars($data['user_token']) : '' ?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="enabled" <?php echo isset($data['enabled']) && $data['enabled'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Enabled extension') ?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Host'); ?></label>
        <input type="text" class="form-control" placeholder="http://example.com" name="host" value="<?php echo (isset($data['host']) && !empty($data['host'])) ? htmlspecialchars($data['host']) : 'http://example.com//apirest.php' ?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="throw_exceptions" <?php echo isset($data['throw_exceptions']) && $data['throw_exceptions'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Throw exceptions, usefull debug mode') ?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="create_duplicate_issues" <?php echo isset($data['create_duplicate_issues']) && $data['create_duplicate_issues'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Create duplicate issues') ?></label>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Create issue on these events automatically') ?></h4>
    <div class="form-group">
        <label><input type="checkbox" value="on" name="chat_close" <?php echo isset($data['chat_close']) && $data['chat_close'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'On chat close') ?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="offline_request" <?php echo isset($data['offline_request']) && $data['offline_request'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'On offline request') ?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="chat_create" <?php echo isset($data['chat_create']) && $data['chat_create'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'On new chat') ?></label>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Issue details'); ?></h4>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="use_email" <?php echo isset($data['use_email']) && $data['use_email'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Use E-mail as visitor name. Will be used to e-mail send name attribute'); ?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Do not use chat nick and use this text value as client name'); ?></label>
        <input class="form-control" type="text" name="static_username" value="<?php echo isset($data['static_username']) ? htmlspecialchars($data['static_username']) : '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'If chat does not have an e-mail use this'); ?></label>
        <input class="form-control" type="text" name="static_email" value="<?php echo isset($data['static_email']) ? htmlspecialchars($data['static_email']) : '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Subject'); ?></label>
        <input type="text" class="form-control" placeholder="New ticket from Live Support {nick} {email} {country_code} {country_name} {city} {user_tz_identifier}" name="subject" value="<?php echo isset($data['subject']) && !empty($data['subject']) ? htmlspecialchars($data['subject']) : 'New ticket from Live Support {nick} {email} {country_code} {country_name} {city} {user_tz_identifier}' ?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="is_html_body" <?php echo isset($data['is_html_body']) && $data['is_html_body'] == true ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'HTML') ?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Message'); ?></label>
        <textarea class="form-control" rows="10" name="message"><?php if (isset($data['message']) && !empty($data['message'])) : ?><?php echo htmlspecialchars($data['message']) ?><?php else : ?><?php echo "Chat ID - {id}\nUser nick - {nick},\nUser e-mail - {email},\nUser time zone - {user_tz_identifier}\nChat was created at - {time_created_front}\n\n//---------------//\nURL to view a chat (url provided if chat exists)\n{url}\n\n//---------------//\nReferer\n{referrer}\n\n//---------------//\nChat log\n{messages}\n\n//---------------//\nOperator remarks\n{remarks}\n\n//----------------//\nUser geo data:\nCountry code - {country_code} {country_name} {city}\n\n//----------------//\nAdditional chat data\n{additional_data}'"; ?><?php endif; ?></textarea>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Message offline'); ?></label>
        <textarea class="form-control" rows="10" name="message_offline"><?php if (isset($data['message_offline']) && !empty($data['message_offline'])) : ?><?php echo htmlspecialchars($data['message_offline']) ?><?php else : ?><?php echo "User nick - {nick}\nUser e-mail - {email}\nProcessed at - {time_created_front}\n\n//---------------//\nReferer\n{referrer}\n\n//---------------//\nUser message\n{message}\n\n//----------------//\nUser geo data:\nCountry code - {country_code} {country_name} {city}\n\n//----------------//\nAdditional data\n{additional_data}"; ?><?php endif; ?></textarea>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="Update" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'Save'); ?>">
    </div>

</form>