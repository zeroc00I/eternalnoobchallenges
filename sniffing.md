### Resolução do desafio sniffing

1. Acessamos o endereço do desafio: https://bountyleaks.cf/challenge/sniffing.php
2. Um redirecionamento é feito para https://bountyleaks.cf/challenge/sniffing.php?message=You%20was%20successfully%20logged%20out
3. Se alterarmos o texto, inclusive com tags html, vemos que ela é refletida na página:
[https://bountyleaks.cf/challenge/sniffing.php?message=<button>Teste</teste>](https://bountyleaks.cf/challenge/sniffing.php?message=&lt;button&gt;Teste&lt;/teste&gt;)
4. Mas, se tentarmos triggar um XSS com ```<script>alert(document.domain)</script>``` o alerta não é triggado. Por quê?

4.1 Para entender essa questão, acesse [https://bountyleaks.cf/challenge/sniffing.php?message=<script>alert(document.domain)</script>](https://bountyleaks.cf/challenge/sniffing.php?message=%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E) e abra o console do navegador, apertando ctrl + shit + i e clicando na aba console

4.2 Observe o erro retornado pelo navegador devido à tentativa de execução de script nessa página:
```
Refused to execute inline script because it violates the following Content Security Policy directive: "script-src 'self' sha256-Cn8Hzq+wmgPb5X2xqCqolSgXTEGPQNOsUufW22rF2Pg='". Either the 'unsafe-inline' keyword, a hash ('sha256-X6WoVv8sUlFXk0r+MI/R+p2PsbD1k74Z+jLIpYAjIgE='), or a nonce 'nonce-...') is required to enable inline execution.
```

4.3 O erro relata que o script não foi possível executar devido à política CSP vigente nessa aplicação.
O que faz sentido, pois ao vermos o código fonte da página, vemos o CSP declarado começando assim:
```
<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self'; script-src 'self'>
```

4.4 Repare que na parte de scripts, somente existe o valor 'self', ou seja, caso formos inserir qualquer <script src='url'> a url obrigatoriamente precisa ter a mesma origem do site acessado, devido ao parâmetro "self" (portanto bountyleaks.cf)

4.5 Outra observação interessante é que não existe o valor unsafe-inline, desse forma qualquer xss inline não será executado pois não foi de forma descrita liberado.
XSS como ```<script>alert(1)</script>```, ```<img src=x onerror=alert()>``` não serão executados, exibindo erro de CSP no console tambem.

  5. Sabemos então, que com o atual contexto, não conseguiremos triggar um xss usando o parâmetro message  (https://bountyleaks.cf/challenge/sniffing.php?message=)
6. Olhando mais o site, vemos que um script é importado de uma outra página:
```<script src='/version.php'></script>```
7. Você entende agora por que esse script não é bloqueado pelo CSP? Pois ele atende ao requisito do CSP, uma vez que se encaixa no contexto 'self', sendo de mesma origem, pois seu endereço é [https://bountyleaks.cf/challenge/version.php](https://bountyleaks.cf/challenge/version.php)
8. Ao abrirmos a página version.php vimos a principio um JS.
9.  Caso façamos um fuzzing de parametros nessa página (https://bountyleaks.cf/challenge/version.php?FUZZ=teste), descobrimos que existe um parametro que reflete na página: version
10. Portanto, [https://bountyleaks.cf/challenge/version.php?version=<script>alert(1337)</script>](https://bountyleaks.cf/challenge/version.php?version=<script>alert(1337)</script>), refletirá na página o payload
11. Pronto, achamos um XSS nessa página? Hm, também não. Por quê? Por que a página se você ver a resposta da página, verá que ela possui content-type application/json. Dessa forma, o conteúdo da página não está sendo tratado como um javascript, ou como html. O navegador não irá renderizar tags a fim de criar um alerta.
12. Agora chega o momento que devemos pensar que :
- Temos a página sniffing com o parametro message sendo refletido, podendo injetar html, mas o CSP exige que a tag script tenha mesma origem no parametro src
- Temos uma página (de mesma origem) que reflete o conteudo de um parâmetro, mas seu content-type é application/json
13. Dessa forma, podemos incluir a segunda página na primeira (embedding), usando a tag <script>.
13.1 Vamos primeiro fazer uma modificação na segunda página para vocês verem que será refletida na primeira:
```https://bountyleaks.cf/challenge/version.php?version=123%27,%27teste%27:%271```

Veja que criamos uma nova chave e valor no JSON da página, chamado teste

13.2 Agora, vamos incluir essa URL na primeira página, ficando assim:
```https://bountyleaks.cf/challenge/sniffing.php?message=<script%20src=%27https://bountyleaks.cf/challenge/version.php?version=123%2527,%2527teste%2527:%25271337%27></script><!--```

- Nesta URL fizemos algumas modificações:
Alteramos a aspas simples (%27) para double encoding (%2527), não para bypassar algo, mas se você visse no código fonte, veria que caso não o fizessemos, quebraria a estrutura da pagina
- A fim de demonstrar o cenário refletido, acrescemos um <!-- ao final, o que comentará todo o resto da página (desabilitando suas execuções posteriores). A explicação é que estamos incluindo a página /version.php dentro de um script, com argumentos nossos.
Mas logo a baixo, existe um outro <script src='version.php'> (nativo da página). Isto sobrescreveria nossos valores, e no momento quero mostrar para vocês que os valores estão refletindo, antes de triggar o XSS.

13.3 Após acessar a URL do item 13.2, abrimos o console do navegador e digitamos app.teste
Veja que o valor 1337 é retornado
13.4 Conseguimos incluir um script arbitrário! Agora só falta modificá-lo para ser um xss!

14. Sabendo que a chave pra resolver o desafio é manipular a segunda página para quando for processada pela primeira em formado de Javascript, vamos criar a segunda URL dessa forma:

```
https://bountyleaks.cf/challenge/version.php?version=123%27};alert(document.domain)//
```

E incluiremos na primeira página, ficando dessa forma:

```
https://bountyleaks.cf/challenge/sniffing.php?message=<script%20src=%27https://bountyleaks.cf/challenge/version.php?version=123%2527};alert(document.domain)//%27></script>
```

15. Desafio resolvido \o/

Você acaba de bypassar duas convicções dos desenvolvedores:

que o CSP da primeira página por ser de mesma origem é seguro e que usando o content-type application/json nunca abre margem para um xss (aqui usamos uma tecnica chamada content sniffing ou mime sniffing)

