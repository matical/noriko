<?php

namespace x;

use RuntimeException;

class Server
{
    /**
     * @var string
     */
    protected $lumenDirectory = __DIR__ . '/public/lumen.php';

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $failures = 0;

    /**
     * @param $port
     */
    public function __construct($port)
    {
        $this->port = $port;
    }

    public function start()
    {
        $pid = exec("php -S localhost:{$this->port} {$this->lumenDirectory} > /dev/null 2>&1 & echo $!");
        $this->onShutdown($pid);

        while (@file_get_contents("http://localhost:{$this->port}/") === false) {
            if ($this->failures >= 3) {
                print "Failed to listen on http://localhost:{$this->port}\n";
                exit(1);
            }

            $this->failures++;
            print "No response from test server.\n";
            sleep(1);
        }
    }

    /**
     * @param int $pid
     */
    public function onShutdown($pid)
    {
        register_shutdown_function(function () use ($pid) {
            exec("kill $pid");
        });
    }

    /**
     * @param int $port
     */
    public static function boot($port = 8888)
    {
        if (stripos(PHP_OS, 'WIN') !== false) {
            throw new RuntimeException("You'll need to run the test server manually on Windows with 'php -S localhost:8888 /path/to/lumen'");
        }

        (new static($port))->start();
    }
}
