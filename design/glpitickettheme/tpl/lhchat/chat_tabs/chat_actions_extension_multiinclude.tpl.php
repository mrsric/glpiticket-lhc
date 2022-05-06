<?php include(erLhcoreClassDesign::designtpl('lhglpiticket/glpiticket_enabled_pre.tpl.php')); ?>
<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhglpiticket','use')  && $glpiticket_module_enabled_pre == true) : ?>
    <?php $chatVariables = $chat->chat_variables_array;
    if (!isset($chatVariables['glpi_ticket_id'])) : ?>
    <a class="btn btn-secondary btn-xs" style="color:#fff" ng-non-bindable id="glpi-tickter-<?php echo $chat->id?>" onclick="return glpiTicket.createTicket('<?php echo $chat->id?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('glpiticket/createanissue','Create a ticket in GLPI')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('glpiticket/createanissue','Create a ticket')?></a>
    <?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhglpiticket/ticket_url.tpl.php'));?>
    <?php endif;?>
<?php endif;?>
