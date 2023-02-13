Chall nível 1 - https://bountyleaks.cf/challenge/simple_waf_1.php
1. Ao acessar a página o retorno é ```Please provide search parameter```, mostrando que a aplicação precisa de um parâmetro search a ser provido pelo usuário. (Uffa, não vamos precisar fazer fuzzing de parametros pra ver qual reflete) **// a real é os labs não terem intenção de guessing / ser o mais whitebox possível**
2.  Acessamos https://bountyleaks.cf/challenge/simple_waf_1.php?search=teste
3. Vemos que a palavra teste é refletida
4. Acessamos ```https://bountyleaks.cf/challenge/simple_waf_1.php?search=<img>``` e percebemos que nossa requisição é vista como maliciosa pela aplicação, já que o retorno é que nossa requisição foi bloqueada
5. Acessamos ```https://bountyleaks.cf/challenge/simple_waf_1.php?search=<teste>``` e percebemos que a tag reflete sem problemas.
6. Concluímos, então, que para triggarmos o XSS, precisamos descobrir uma forma de bypassar esse bloqueio, provavelmente feito com base em tags HTML aceitas
7. Aqui você pode tomar dois caminhos:

7.1 Você descobre que a aplicação não permite ```<img>``` mas permite ```<IMG>``` (é case sensitive)
Então você termina o desafio com algo tipo: https://bountyleaks.cf/challenge/simple_waf_1.php?search=%3CIMG%20src=x%20onerror=alert(document.domain)%3E

7.2 Você evita utilizar tags conhecidas e parte pra uma abordagem customizada.

Tendo em vista que essa é a resposta do próximo desafio (simple_waf_2.php), recomendamos que você o tente fazer primeiramente, para somente então encontrar a resolução deste segundo em:

https://github.com/zeroc00I/eternalnoobchallenges/blob/main/simples_waf_2.md

## Parabéns, você conseguiu triggar o XSS na página \o/
  
O código fonte desse desafio pode ser encontrado aqui:
https://github.com/zeroc00I/eternalnoobchallenges/blob/main/1-simple-waf.php
