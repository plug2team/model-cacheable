<?php


namespace Plug2Team\ModelCacheable\Commands;


use Illuminate\Console\Command;
use Plug2Team\ModelCacheable\Concerns\Cacheable;
use Plug2Team\ModelCacheable\Index;
use Plug2Team\ModelCacheable\Strategy;

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
     * @return void
     */
    public function handle()
    {
        /**
         * @var string $class
         * @var  Index $index
         */
        foreach (app('cacheable')->getModels() as $class => $index) {
            $index->flush();
        }
    }
}
