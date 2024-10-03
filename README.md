# painel-debate
Painel para gerência de tempo e perguntas em debates para eleições. Você poderá utilizar como fonte para transmissões com OBS.

## Requisitos
 
- Um servidor Web com suporte a PHP ativo (pode ser Apache HTTP Server, nginx etc.);
- um banco de dados, que pode ser MySQL ou SQLite (outros bancos de dados não foram testados)
- sistema operacional compatível. Nos testes foram usados Windows e Linux. Caso você deseje compartilhar os recursos de usuários externos, seu servidor Web deve estar com firewall configurado para ser acessível externamente.

## Instalação

- Baixe os arquivos para seu diretório Web (`git clone https://github.com/TiagoDGomes/painel-debate.git`);
- Copie o arquivo `config.php-default` como `config.php`;
- Abra o arquivo `config.php` e configure a seu gosto:
  - se decidir por MySQL, deixe configurado uma nova database;
  - se decidir por SQLite, verifique se há permissões de escrita para o arquivo definido no arquivo de configuração (ex.: `chown -R www-data:www-data painel-debate`);
- No seu diretório Web, execute `php install.php`;
- Abra o navegador e navegue em http://seu-servidor/painel-debate/.

## Tutorial rápido para utilização

Antes de começar, é preciso criar um painel. A página principal já vem para a configuração de um novo painel. 
Recomendamos deixar os valores padrão. Clicando em "Criar novo painel", serão exibidos dois links: de usuário e de gerência. 
O link de usuário pode ser compartilhado para mais pessoas, mas o de gerência deve ser restrito a quem irá comandar as ações do painel. 

### Tela de gerência
A tela de gerência contém os todos os controles do painel: crônometro, mensagem e roleta.

#### Cronômetro
    
Na seção "Cronômetro" é possível definir o tempo para o cronômetro. 
O botão "Preparar" faz mostrar pausado o tempo escolhido na tela para todos os participantes e o botão "Iniciar" dá início ao cronômetro (este se torna "Pausar" caso o cronômetro esteja ativo)

#### Mensagem pública

Na seção "Mensagem pública" é possível enviar mensagens aos usuários participantes. Essa mensagem é visível para todos.
A opção "Auto-enviar mensagem após a escolha do número sorteado" envia o texto da roleta de sorteio automaticamente após a geração do número de sorteio.

#### Roleta para sorteio

Na seção "Roleta" é possível carregar alternativas para poder sorteá-las. Essas alternativas podem ser perguntas, opções ou qualquer outra coisa que possa ser sorteável.

Na caixa de seleção de opções conterá todos os grupos de alternativas que foram carregados. 
Para carregar um novo grupo, clique no link "Escolher um arquivo texto com perguntas para uma nova roleta". 
O arquivo a ser enviado deverá ser um arquivo de texto simples contendo em cada linha uma opção a ser sorteável.

Com todas as perguntas carregadas, já é possível fazer um sorteio. Selecione a opção de perguntas e clique "Iniciar sorteio de números aleatórios aos participantes"
Todos que estão com link de usuário e de gerência irão poder gerar números aleatórios. Note que é preciso movimentar o mouse para gerar números aleatórios (haverá indicação dos números em circulos caindo para baixo da tela). 
Você precisa deixar cerca de 2 segundos (ou mais) para que todos consigam enviar seus números aleatórios (quanto maior o tempo e maior o número de usuários, mais aleatório será). 

Clique em "Parar sorteio de números aleatórios aos participantes" para obter o número sorteado. O número sorteado será o resultado do resto da divisão da soma de todos os números gerados pelo número de perguntas cadastradas do grupo, mais um.  

### Tela de usuário

A tela de usuário é praticamente a mesma da gerência, exceto pela ausência dos formulários de ações. 
A única ação possível do usuário é de gerar números aleatórios no momento em que a pessoa gerente iniciar o sorteio.

## Créditos

Desenvolvido pela equipe da CTI do IFSP Câmpus São João da Boa Vista.
