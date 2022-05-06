<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{	    
    try {
        $tpl = erLhcoreClassTemplate::getInstance('lhglpiticket/createanissue.tpl.php');        
        $glpiTicket = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionGlpiticket');
        
        $glpiTicket->getConfig();
        
        if (!isset($glpiTicket->configData['enabled']) || $glpiTicket->configData['enabled'] == false) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('glpiticket/createanissue','glpiTicket is not enabled. Please enable it.'));
        }
        
        $glpiTicketId = $glpiTicket->createTicketByChat($chat);
        $tpl->set('chat',$chat);
    	echo json_encode(array('error' => false,'msg' => $tpl->fetch()));
    } catch (Exception $e) {
        echo json_encode(array('error' => true,'msg' => $e->getMessage()));
    }	
	exit;    
} else {
    echo json_encode(array('error' => true,'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('glpiticket/createanissue','You do not have permission to access a chat')));
    exit;
}



?>