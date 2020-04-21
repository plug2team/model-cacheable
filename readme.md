# Model Cached

Pacote para controlar cache em modelos.

## Guia

O pacote requer suporte a cache tags, recomendavel o uso de `redis`.   

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
