<?php

namespace optimy\console;

require_once(__DIR__.'/../../../bootstrap/app.php');


use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;


class OneShot extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->output = new ConsoleOutput;
        if (!in_array('start', get_class_methods(get_called_class()))) {
            throw new Exception('A ::start() method entry point should be implemented in your class.');
        }
    }

    /**
     * Poorman's progress indicator. :(
     *
     * @return void
     */
    public function progress($char = null, $count = 1)
    {
        print(str_repeat($char ?: '·', $count));
    }
}


/* boot the Application to initialize the IoC/DI container. */
$app->boot();
