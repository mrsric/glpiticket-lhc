<?php

/**
 * Direct integration with GLPI
 * */
class erLhcoreClassExtensionGlpiticket
{

    public $configData = false;
    public $userId = '';
    const URIAPI = '/apirest.php/';

    public function __construct()
    {
    }

    public function run()
    {
        $this->registerAutoload();

        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

        /**
         * We listen to all events, but check is done only in even method. This way we save 1 disk call for configuraiton file read.
         * */

        /**
         * User events
         */
        $dispatcher->listen('chat.close', array($this, 'chatClosed'));
        $dispatcher->listen('chat.chat_started', array($this, 'chatCreated'));
        $dispatcher->listen('chat.chat_offline_request', array($this, 'chatOfflineRequest'));
    }

    public function registerAutoload()
    {
        spl_autoload_register(array(
            $this,
            'autoload'
        ), true, false);
    }

    public function autoload($className)
    {
        $classesArray = array(
            'erLhcoreClassGLPITicketValidator' => 'extension/glpiticket/classes/erlhcoreclassglpiticketvalidator.php'
        );

        if (key_exists($className, $classesArray)) {
            include_once $classesArray[$className];
        }
    }

    public function getConfig()
    {
        if ($this->configData === false) {
            $glpiOptions = erLhcoreClassModelChatConfig::fetch('glpi_options');
            $data = (array) $glpiOptions->data;
            $this->configData = $data;
        }
    }

    public function getUriBase()
    {
        $this->getConfig();
        return $this->configData['host'] . erLhcoreClassExtensionGlpiticket::URIAPI;
    }

    public function initCurlSessionPost($urlGlpi, $data, $sessionToken){        
        
        $ch = $this->initCurlSession($urlGlpi, $sessionToken);        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        return $ch;

    }

    public function initCurlSessionGet($urlGlpi, $sessionToken){        
        
        $ch = $this->initCurlSession($urlGlpi, $sessionToken);        
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        return $ch;

    }

    public function initCurlSession($urlGlpi, $sessionToken){

        $ch = $this->initCurl($urlGlpi);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:',
            'Content-Type: ' . 'application/json',
            'app_token: ' . $this->configData['app_token'],
            'app-token: ' . $this->configData['app_token'],
            'session_token: ' . $sessionToken,
            'session-token: ' . $sessionToken
        ));

        return $ch;
    }

    public function initCurl($urlGlpi){

        $this->getConfig();  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlGlpi);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GLPI API Client');
        curl_setopt($ch, CURLOPT_HEADER, 0);       
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($this->configData['disable_ssl_verify'] == true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }        

        return $ch;
    }

    public function execCurl($ch){

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errors = curl_error($ch);
        curl_close($ch);

        $resultExec = array(
            'result' => $result,
            'code' => $code,
            'errors' => $errors,
        ); 

        return $resultExec;
    }

    public function validateStatusCode($result, $msg){

        if ($result['code'] != 200 && $result['code'] != 201) {
            throw new Exception($msg . ' ' . $result['result']);
        }
    }

    public function sendRequest($data)
    {
        $sessionToken = $this->initSession();

        $replaceUserId = str_replace('{glpi_ticket_userid}', $this->userId, json_encode($data));
        $data = $replaceUserId;

        $urlGlpi = $this->getUriBase() . 'Ticket';

        $ch = $this->initCurlSessionPost($urlGlpi, $data, $sessionToken);   
        $result = $this->execCurl($ch);

        $this->validateStatusCode($result, 'Unable to create ticket:');

        $ticket_id = json_decode($result['result'])->id;

        $this->killSession($sessionToken);

        return $ticket_id;
    }

    public function addUserOnTicket($sessionToken, $ticketId, $data)
    {
        $urlGlpi = $this->getUriBase() . 'Ticket/' . $ticketId . '/Ticket_User';

        $ch = $this->initCurlSessionPost($urlGlpi, $data, $sessionToken); 
       
        $result = $this->execCurl($ch);

        $this->validateStatusCode($result, 'Unable add User on ticket: ' . $ticketId);

        $ticket_id = (int) $result['result'];

        //$this->killSession($sessionToken);

        return $ticket_id;
    }

    public function initSession()
    {
        $urlGlpi = $this->getUriBase() . 'initSession';

        $ch = $this->initCurl($urlGlpi);        
        curl_setopt($ch, CURLOPT_HTTPGET, 1);   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:',
            'app_token: ' . $this->configData['app_token'],
            'app-token: ' . $this->configData['app_token'],
            'Authorization: user_token '. $this->configData['user_token'],
        ));

        $result = $this->execCurl($ch);      

        $this->validateStatusCode($result, 'Unable to create session_token:');

        $token = json_decode($result['result']);
        $sessionToken = $token->session_token;  

        $this->userId = $this->getFullSession($sessionToken);

        return $sessionToken;
    }

    public function killSession($sessionToken)
    {
        $urlGlpi = $this->getUriBase() . 'killSession';

        $ch = $this->initCurlSessionGet($urlGlpi, $sessionToken);        
        $result = $this->execCurl($ch);
        
        return $result['code'];
    }

    public function getFullSession($sessionToken)
    {
        $urlGlpi = $this->getUriBase() . 'getFullSession';

        $ch = $this->initCurlSessionGet($urlGlpi, $sessionToken);         
        $result = $this->execCurl($ch);      

        $this->validateStatusCode($result, 'Unable to get Session:');

        $session = json_decode($result['result']);

        return $session->session->glpiID;
    }


    public function getIssueUrl($issueId)
    {
        $this->getConfig();
        return $this->configData['host'] . '/front/ticket.form.php?id=' . $issueId;
    }

    public function fillDataByChat($chat)
    {
        $this->getConfig();

        $messages = array_reverse(erLhcoreClassModelmsg::getList(array(
            'limit' => 5000,
            'sort' => 'id DESC',
            'filter' => array(
                'chat_id' => $chat->id
            )
        )));

        $messagesContent = '';
        foreach ($messages as $msg) {
            if ($msg->user_id == -1) {
                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat, $msg->time) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin', 'System assistant') . ': ' . htmlspecialchars($msg->msg) . "\n";
            } else {
                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat, $msg->time) . ' ' . ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)) . ': ' . htmlspecialchars($msg->msg) . "\n";
            }
        }

        $data =
            array('input' =>
            array(
                //'name' => ((isset($this->configData['use_email']) && $this->configData['use_email'] == true && $chat->email != '') ? $chat->email : ((isset($this->configData['static_username']) && $this->configData['static_username'] != '') ? $this->configData['static_username'] : $chat->nick)),
                //'email' => $chat->email == '' ? ((isset($this->configData['static_email']) && $this->configData['static_email'] != '') ? $this->configData['static_email']  : 'no-email@' . $_SERVER['HTTP_HOST']) : $chat->email,
                'name' =>
                str_replace(
                    array(
                        '{id}',
                        '{department}',
                        '{referrer}',
                        '{nick}',
                        '{phone}',
                        '{email}',
                        '{country_code}',
                        '{country_name}',
                        '{city}',
                        '{user_tz_identifier}'
                    ),
                    array(
                        (string)$chat->id,
                        (string)$chat->department,
                        $chat->referrer,
                        $chat->nick,
                        $chat->phone,
                        $chat->email,
                        $chat->country_code,
                        $chat->country_name,
                        $chat->city,
                        $chat->user_tz_identifier
                    ),
                    $this->configData['subject']
                ),
                'content' =>
                nl2br(str_replace(array(
                    '{department}',
                    '{time_created_front}',
                    '{additional_data}',
                    '{id}',
                    '{url}',
                    '{referrer}',
                    '{messages}',
                    '{remarks}',
                    '{nick}',
                    '{email}',
                    '{phone}',
                    '{country_code}',
                    '{country_name}',
                    '{city}',
                    '{user_tz_identifier}'
                ), array(
                    (string)$chat->department,
                    date(erLhcoreClassModule::$dateDateHourFormat, $chat->time),
                    $chat->additional_data,
                    $chat->id,
                    (erLhcoreClassSystem::$httpsMode == true ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login') . '/(r)/' . rawurlencode(base64_encode('chat/single/' . $chat->id)),
                    $chat->referrer,
                    $messagesContent,
                    $chat->remarks,
                    $chat->nick,
                    $chat->email,
                    $chat->phone,
                    $chat->country_code,
                    $chat->country_name,
                    $chat->city,
                    $chat->user_tz_identifier
                ), $this->configData['message'])),
                '_users_id_requester' => '{glpi_ticket_userid}',
                'users_id' => '{glpi_ticket_userid}',
                'requesttypes_id' => 1,
                //'ip' => $chat->ip
            ));

        return $data;
    }

    public function chatCreated($params)
    {
        $this->getConfig();

        if (isset($this->configData['enabled']) && $this->configData['enabled'] == true && $this->configData['chat_create'] === true && ($this->configData['create_duplicate_issues'] == true || !isset($params['chat']->chat_variables_array['glpi_ticket_id']))) {
            try {
                $data = $this->fillDataByChat($params['chat']);
                $ticketId = $this->sendRequest($data);
                $this->assignChatGLPITicketId($params['chat'], $ticketId);
            } catch (Exception $e) {
                if ($this->configData['throw_exceptions'] == true) {
                    throw $e;
                }
            }
        }
    }

    public function chatOfflineRequest($params)
    {
        $this->getConfig();

        if (isset($this->configData['enabled']) && $this->configData['enabled'] == true && $this->configData['offline_request'] === true) {
            $chat = $params['chat'];
            $inputData = $params['input_data'];

            $data =
                array('input' =>
                array(
                    'name' => str_replace(
                        array(
                            '{department}',
                            '{referrer}',
                            '{nick}',
                            '{email}',
                            '{phone}',
                            '{country_code}',
                            '{country_name}',
                            '{city}',
                            '{user_tz_identifier}'
                        ),
                        array(
                            (string)$chat->department,
                            $chat->referrer,
                            $chat->nick,
                            $chat->email,
                            $chat->phone,
                            $chat->country_code,
                            $chat->country_name,
                            $chat->city,
                            $chat->user_tz_identifier
                        ),
                        $this->configData['subject']
                    ),
                    'content' => str_replace(array(
                        '{department}',
                        '{time_created_front}',
                        '{additional_data}',
                        '{referrer}',
                        '{message}',
                        '{nick}',
                        '{email}',
                        '{phone}',
                        '{country_code}',
                        '{country_name}',
                        '{city}',
                        '{user_tz_identifier}'
                    ), array(
                        (string)$chat->department,
                        date(erLhcoreClassModule::$dateDateHourFormat, time()),
                        $chat->additional_data,
                        (isset($_POST['URLRefer']) ? $_POST['URLRefer'] : ''),
                        $inputData->question,
                        $chat->nick,
                        $chat->email,
                        $chat->phone,
                        $chat->country_code,
                        $chat->country_name,
                        $chat->city,
                        $chat->user_tz_identifier
                    ), $this->configData['message_offline']),
                    '_users_id_requester' => '{glpi_ticket_userid}',
                    'users_id' => '{glpi_ticket_userid}',
                    'requesttypes_id' => 1,
                    //'ip' => $chat->ip
                ));

            try {
                $ticketId = $this->sendRequest($data);
            } catch (Exception $e) {
                if ($this->configData['throw_exceptions'] == true) {
                    throw $e;
                }
            }
        }
    }


    public function assignChatGLPITicketId(erLhcoreClassModelChat &$chat, $ticketId)
    {
        /**
         * Remember created issue id
         * */
        $variablesArray = $chat->chat_variables_array;
        $variablesArray['glpi_ticket_id'] = $ticketId;
        $chat->chat_variables = json_encode($variablesArray);
        $chat->chat_variables_array = $variablesArray;
        $chat->updateThis();
    }

    public function createTicketByChat(erLhcoreClassModelChat &$chat)
    {
        $this->getConfig();
        if ((isset($this->configData['enabled']) && $this->configData['enabled'] == true) && ($this->configData['create_duplicate_issues'] === true || !isset($chat->chat_variables_array['glpi_ticket_id']))) {
            $data = $this->fillDataByChat($chat);
            $ticketId = $this->sendRequest($data);
            $this->assignChatGLPITicketId($chat, $ticketId);
            return $ticketId;
        } else {
            throw new Exception('Issue was already created');
        }
    }

    public function chatClosed($params)
    {
        $this->getConfig();
        if ((isset($this->configData['enabled']) && $this->configData['enabled'] == true) && $this->configData['chat_close'] === true && ($this->configData['create_duplicate_issues'] === true || !isset($params['chat']->chat_variables_array['glpi_ticket_id']))) {
            try {
                $data = $this->fillDataByChat($params['chat']);
                $ticketId = $this->sendRequest($data);
                $this->assignChatGLPITicketId($params['chat'], $ticketId);
            } catch (Exception $e) {
                if ($this->configData['throw_exceptions'] == true) {
                    throw $e;
                }
            }
        }
    }
    
}
