<?php include(erLhcoreClassDesign::designtpl('lhglpiticket/glpiticket_enabled_pre.tpl.php')); ?>
<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhglpiticket','manage') && $glpiticket_module_enabled_pre == true) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('glpiticket/index')?>"><i class="material-icons">task</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket','GLPI Settings');?></a></li>
<?php endif;?>