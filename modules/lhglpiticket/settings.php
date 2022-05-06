<?php
$tpl = erLhcoreClassTemplate::getInstance('lhglpiticket/settings.tpl.php');

$glpiTicketOptions = erLhcoreClassModelChatConfig::fetch('glpi_options');
$data = (array) $glpiTicketOptions->data;

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassGLPITicketValidator::validateSettings($data);

    if (count($Errors) == 0) {
        try {
            $glpiTicketOptions->explain = '';
            $glpiTicketOptions->type = 0;
            $glpiTicketOptions->hidden = 1;
            $glpiTicketOptions->identifier = 'glpi_options';
            $glpiTicketOptions->value = serialize($data);
            $glpiTicketOptions->saveThis();
            
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', array(
                $e->getMessage()
            ));
        }

    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('data',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/glpiticket', 'GLPI integration settings')
    )
);

?>