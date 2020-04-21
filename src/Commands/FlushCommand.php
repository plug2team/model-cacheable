<?php


namespace Plug2Team\ModelCached\Commands;


use Illuminate\Console\Command;
use Plug2Team\ModelCached\Concerns\Cacheable;
use Plug2Team\ModelCached\Strategy;

class FlushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cacheable:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'flush caches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $models = config('model_cached.models');

        foreach ($models as $model) {
            $reflection = new \ReflectionClass($model);

            if(!in_array(Cacheable::class, $reflection->getTraitNames())) continue;

            $cache_tag = str_replace('\\', '.', strtolower($model));

            /** @var Strategy $strategy */
            $strategy = app(Strategy::class, ['tag' => $cache_tag]);

            $strategy->flush();
        }
    }
}
