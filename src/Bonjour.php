<?php

namespace Valet;

use Exception;
use Symfony\Component\Process\Process;

class Bonjour
{
    /**
     * Register sites with Bonjour.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    public static function prepare($path)
    {
        $sites = array_filter(glob("{$path}/*"), 'is_dir');
        array_filter($sites, 'trim');
        array_map(function ($site) {
            static::register(basename(strtolower($site)));
        }, $sites);
    }

    public static function register($path)
    {
        $command = "dns-sd -P {$path} _http._tcp local 80 {$path}.local 127.0.0.1";
        $outputFile = tempnam('~', 'valet_');
        $output = '';
        $timer = 0;
        $sleepInterval = 500;
        $file = fopen($outputFile, 'r');
        // var_dump($path);
        // die();
        // register each foldername.local in Bonjour to point to localhost
        exec(sprintf("%s > %s 2>&1 &", $command, $outputFile));
        while ($timer < 5000 && $output === '') {
            if (filesize($outputFile)) {
                $output = fread($file, filesize($outputFile));
                var_dump($output);
            }

            $timer += $sleepInterval;
            time_nanosleep(0, $sleepInterval);
            var_dump($path, $timer, $output);
        }

        fclose($file);

        // unlink($outputFile);
    }
}
