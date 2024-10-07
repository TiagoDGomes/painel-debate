# painel-debate
Painel para gerência de tempo e perguntas em debates para eleições. Você poderá utilizar como fonte para transmissões com OBS.

## Requisitos
 
- um servidor Web com suporte a PHP ativo (pode ser Apache HTTP Server, nginx etc.);
- suporte a SQLite no PHP;
- sistema operacional compatível (nos testes foram usados Windows e Linux);
- caso você deseje compartilhar os recursos de usuários externos, seu servidor Web deve estar com firewall configurado para ser acessível externamente.

## Instalação

- Baixe os arquivos para seu diretório Web (`git clone https://github.com/TiagoDGomes/painel-debate.git`);
- Copie o arquivo `config.php-default` como `config.php`;
- Abra o arquivo `config.php` e configure a seu gosto:
- verifique se há permissões de escrita para a pasta "database" no arquivo de configuração (ex.: `chown -R www-data:www-data databases`);
- Abra o navegador e navegue em http://seu-servidor/painel-debate/.

## Créditos

Desenvolvido pela equipe da CTI do IFSP Câmpus São João da Boa Vista.
