# painel-debate

Painel para gerência de tempo em debates para eleições. O sistema fornece links para uso múltiplo, que permite compartilhar o painel para outros usuários ou até mesmo utilizar como fonte para transmissões com OBS.

## Requisitos

- um servidor Web com suporte a PHP ativo (pode ser Apache HTTP Server, nginx etc.);
- suporte a SQLite no PHP;
- caso você deseje compartilhar os recursos de usuários externos, seu servidor Web deve estar acessível externamente (verifique seu firewall).

## Instalação

- Clone o projeto usando `git clone https://github.com/TiagoDGomes/painel-debate.git` em seu diretório Web (ou baixe [aqui](https://github.com/TiagoDGomes/painel-debate/releases/latest) a última versão);
- Copie o arquivo `config.php-default` como `config.php`;
- Edite o arquivo `config.php` e configure a seu gosto:
- Verifique se há permissões de escrita para a pasta "database" no arquivo de configuração (ex.: `chown -R www-data:www-data databases`);
- Abra o navegador e navegue em http://seu-servidor/painel-debate/.

## Créditos

Desenvolvido pela equipe da CTI do IFSP Câmpus São João da Boa Vista.
