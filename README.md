# LiveHelperChat - GLPI


## _Integração entre o LHC_WEB o Sistema de GLPI_

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://#)

### Fork do projeto osTicker [osTicket](https://github.com/LiveHelperChat/osTicket) para o LHC por [remdex](https://github.com/remdex)

## Features
- Configuração para abertura de chamado no Chat
- Opção de Gerenciamento para confguração da extensão
- Criação do chamado via clique do Botão
- Automaticamente
    - Aberturado chat
    - Requisição offline
	- Fechamento do chat

### Configuração GLPI: 

Habilite a API Rest no GLPI pelo caminho:

```
Configurar -> Geral -> API
    * Habilitar API Rest: SIM
    * Habilitar que se faça login com token externo: SIM
```
Adicione um novo cliente de API, conforme necessario:
```
Configurar -> Geral -> API -> Adicionar cliente de API
    * Nome: "Nome para o cliente"
    * Ativo: SIM
    * Registrar log de conexões: LOGS
    * Filtrar acesso: (Deixe esses parâmetros vazios para desabilitar a restrição
                       de acesso à API ou restrinja conforme necessario. Anote o 
                       Token da aplicação (app_token), será necessário informar na tela de configuração da extensão no LHC)
```
Configure o usuário que utilizará a API para criação dos chamados:

```
Administração -> Usuarios
    * Crie um usuário com permissão para abertura de chamados
    * Detro do usuario -> Chaves de acesso remoto -> Gere e anote a chave: API token
        - Será necessário informar na tela de configuração da exntesão no LHC
```
## Instalação

1) Copie a pasta da extensão **glpiticket** para o diretório de instalação do LHC `extension/glpiticket`
2) Edite o arqivo settings.ini.php do servidor LHC e habilite a extensão **glpiticket**, localize e altere a definição conforme exemplo:

```PHP
'extensions' => 
      array (
        0 => 'glpiticket',
 )
```
 4) Acesse o LHC como administrador e limpe o cache em `Configurações -> Sistema -> Limpar o cache`.
 5) No menu esquedo dentro de **Modúlos** aparecerá o menu GLPI, configure conforme seu ambiente:
    - Obrigatório:
        - App Token
        - User Token
        - Host
        
## Utilização

* Exemplo criação e e leitura do chamado.
![exemplo](./doc/LHC%20-%20Extension%20GLPI.gif)

## Referências

| PROJETO | README |
| ------ | ------ |
| LHC OsTicket | https://github.com/LiveHelperChat/osTicket |
| LHC | https://livehelperchat.com |
| LHC GitHub | https://github.com/LiveHelperChat/livehelperchat |

-------------------------
###### [CRECISP: Conselho Regional de Corretores de Imóveis 2ª Região](https://ant.apache.org)


