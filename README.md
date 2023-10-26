# Expert Framework Validation

O **Expert Framework Validation** é uma biblioteca PHP criada para simplificar a validação de dados em suas aplicações. Ele oferece um conjunto de regras de validação que podem ser aplicadas a dados de entrada, com mensagens de erro personalizadas.

Componente pertencente ao framework *Jonaselias\ExpertFramework* https://github.com/jonas-elias/mercado-software-expert

## Instalação 🚀

Para começar a usar o Componente Expert Framework Validation, você pode instalá-lo facilmente via Composer. Basta executar o seguinte comando:

```bash
composer require expert-framework/validation
```

## Uso ✅
A classe Validation oferece um mecanismo flexível para validar dados. Você pode definir regras de validação para campos de entrada e, em seguida, aplicar a validação aos dados. Aqui está um exemplo de como usar a classe Validation:

```php
use ExpertFramework\Validation\Validation;

$validator = new Validation();

$data = [
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'age' => 30,
];

$rules = [
    'username' => 'required|string|min:3|max:255',
    'email' => 'required|string|email|max:255',
    'age' => 'required|integer|min:18',
];

$validator->validate($data, $rules);

if ($validator->fails()) {
    $errors = $validator->errors();
}

```

#### Regras de Validação
O componente suporta várias regras de validação, incluindo:

* required: O campo é obrigatório.
* string: O campo deve ser uma string.
* integer: O campo deve ser um número inteiro.
* float: O campo deve ser um número de ponto flutuante.
* min:n: O campo deve conter pelo menos n caracteres.
* max:n: O campo não deve conter mais de n caracteres.
* exists:table,column: O valor do campo deve existir na tabela especificada na coluna especificada.
* not_exists:table,column: O valor do campo não deve existir na tabela especificada na coluna especificada.

## Dúvidas 🤔
Caso exista alguma dúvida sobre como instalar, utilizar ou gerenciar o projeto, entre em contato com o email: jonasdasilvaelias@gmail.com

Um grande abraço!
