# Model Cached

Pacote para controlar cache em modelos.

## Instalação

> composer require plug2team/model-cached

Após a instalação do pacote, importe o arquivo de configuração:

>  php artisan vendor:publish --tag=config 

## Configurando modelos

Para seu modelo ser observado pelo cache, basta aplicar a trait `Cacheable` em sua model.

```php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Plug2Team\ModelCached\Concerns\Cacheable;

class User extends Authenticatable
{
    use Cacheable;
```

em `AppServiceProvider` no metodo `boot` registre o modelo

```php
    public function boot() {
        \App\User::crape();
    }
```

## Utilidades

Registre os comandos auxiliares em `Console/Kernel.php`.

```php
 $schedule->command('cacheable:flush')->cron(config('model_cached.commands.flush'));
 $schedule->command('cacheable:re_index --all')->cron(config('model_cached.commands.re_index'));
```

## Como funciona

Após alguns testes, observei que é mais facil e eficiente gerenciar a persistencia de registros por agrupamento.

O agrupamento funciona assim: 

1. Convertemos o nome da model de `App\User` para `user`.

2. Após termos o nome, criamos o grupo de indices para nome `user.indexes` onde guardamos e gerenciamos os `ids` recebimos por meio dos
eventos do eloquente.

3 . Tendo esses indices, o proximo passo é criar o agrupamento padrão o grupo `all` pode ser recuperado `` app('cacheable')->index('App\User')->group('all')->retrieve() `` outra forma
de recuperar esse grupo é chamando direto na model `\App\User::cache('all')`. ou acessando via helper `` app('cache')->get('cached.user.all') ``.  

4. Para capturar os indices vinculados ao grupo `` app('cacheable')->index('App\User')->group('all')->getIndexes() `` isso retornara a lista de ids vinculados ao grupo.

## Recuperar indice

A estrutura de chaves é `.id`

## Recuperar grupos

Após registrar a model, passamos a monitorar os eventos do eloquente: `saved`,`deleted`,`retrieved`.

Para recupear um agrupamento é simples, basta informar o grupo em:
  
```php
$users = \App\User::cache('all');
```

O grupo `all` é registrado por padrão, outros grupos podem ser registrados com sua arvores de chaves.



