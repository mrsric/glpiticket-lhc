<?php
$tpl = erLhcoreClassTemplate::getInstance('lhglpiticket/index.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'glpiTicket integration')
    )
);

?>