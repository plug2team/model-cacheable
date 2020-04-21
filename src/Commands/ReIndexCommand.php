<?php


namespace Plug2Team\ModelCached\Commands;


use Illuminate\Console\Command;
use Plug2Team\ModelCached\Concerns\Cacheable;
use Plug2Team\ModelCached\Index;
use Plug2Team\ModelCached\Strategy;

class ReIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cacheable:reindex {model?} {--all}';

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
        /**
         * @var string $class
         * @var  Index $index
         */
        foreach (app('cacheable')->getModels() as $class => $index) {
            //
            app($class)->all()->each->cached();
        }
    }
}
