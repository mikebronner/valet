<?php

namespace Valet;

use Exception;
use Symfony\Component\Process\Process;

class PhpFpm
{
    /**
     * Install and configure PHP.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    public static function install($output)
    {
        // if (! Brew::hasInstalledPhp()) {
        //     static::download($output);
        // }

        if (version_compare(PHP_VERSION, '7.0', '<')) {
            static::download($output);
        }

        static::updateConfiguration();
        static::restart();
    }

    /**
     * Download a fresh copy of PHP 7.0 from Brew.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    public static function download($output)
    {
            $output->writeln('<info>PHP 7.0 is not installed, installing it now ...</info>');
            $installer = run('curl -s http://php-osx.liip.ch/install.sh');
            quietly("sudo bash -s 7.0 {$installer}");


        // $output->writeln('<info>PHP 7.0 is not installed, installing it now via Brew...</info> 🍻');
        //
        // Brew::tap('homebrew/dupes', 'homebrew/versions', 'homebrew/homebrew-php');
        //
        // run('brew install php70', function ($exitCode, $processOutput) use ($output)  {
        //     $output->write($processOutput);
        //
        //     throw new Exception('We were unable to install PHP.');
        // });
    }

    /**
     * Update the PHP FPM configuration to use the current user.
     *
     * @return void
     */
    public static function updateConfiguration()
    {
        quietly('sed -i "" -E "s/^user = .+$/user = '.$_SERVER['SUDO_USER'].'/" '.static::fpmConfigPath());

        quietly('sed -i "" -E "s/^group = .+$/group = staff/" '.static::fpmConfigPath());
    }

    /**
     * Restart the PHP FPM process.
     *
     * @return void
     */
    public static function restart()
    {
        static::stop();
        static::start();

        // Brew::restartLinkedPhp();
    }

    /**
     * Start the PHP FPM process.
     *
     * @return void
     */
    public static function start()
    {
        run(static::phpPath());

        // Brew::stopService('php56', 'php70');
    }

    /**
     * Stop the PHP FPM process.
     *
     * @return void
     */
    public static function stop()
    {
        run('kill `cat $PIDPATH/php-fpm.pid`');

        // Brew::stopService('php56', 'php70');
    }

    /**
     * Get the path to the FPM configuration file for the current PHP version.
     *
     * @return string
     */
    protected static function fpmConfigPath()
    {
        return '/usr/local/php5/etc/php-fpm.d/www.conf.default';

        // if (Brew::linkedPhp() === 'php70') {
            // return '/usr/local/etc/php/7.0/php-fpm.d/www.conf';
        // } else {
            // return '/usr/local/etc/php/5.6/php-fpm.conf';
        // }
    }

    protected static function phpPath()
    {
        return run('which php');
    }
}
