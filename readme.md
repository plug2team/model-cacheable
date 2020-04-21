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


