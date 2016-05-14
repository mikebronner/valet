<?php

namespace Valet;

use Exception;
use Symfony\Component\Process\Process;

class Bonjour
{
    /**
     * Register all parked sites with Bonjour.
     *
     * @param  string          $path
     * @param  OutputInterface $output
     * @return void
     */
    public static function prepare($path, $output)
    {
        $sites = array_filter(glob("{$path}/*"), 'is_dir');
        array_filter($sites, 'trim');
        array_map(function ($site) use ($output) {
            static::register(basename(strtolower($site)), $output);
        }, $sites);

        $output->writeln("");
    }

    /**
     * Register site with Bonjour.
     *
     * @param  string          $path
     * @param  OutputInterface $output
     * @return void
     */
    public static function register($path, $output)
    {
        $command = "dns-sd -P {$path} _http._tcp local 80 {$path}.local 127.0.0.1";
        $outputFile = tempnam('~', 'valet_');
        $fileOutput = '';
        $timer = 0;
        $sleepInterval = .3;

        $file = fopen($outputFile, 'r');
        exec(sprintf("%s > %s 2>&1 & echo $!", $command, $outputFile), $pidArray);
        $pid = $pidArray[0];
var_dump($pid);
        while ($timer < 5 && $fileOutput === '') {
            time_nanosleep(0, $sleepInterval * 1000000000);
            $fileOutput = fread($file, 4096);
            $timer += $sleepInterval;
        }

        fclose($file);
        // run_as_root("kill {$pid}");
        unlink($outputFile);

        $output->writeln("<info>Registestered 'http://{$path}.local'</info> ✔︎");
    }
}
