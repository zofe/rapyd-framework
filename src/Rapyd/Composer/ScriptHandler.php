<?php

/**
 * Based on Bolt\Composer\ScriptHandler
 * @see https://github.com/bolt/bolt/blob/master/app/src/Bolt/Composer/ScriptHandler.php
 */

namespace Rapyd\Composer;

use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{
    public static function deployApp($event)
    {
        $options = self::getOptions($event);
        $webDir = $options['rapyd-web-dir'];
        $dirMode = $options['rapyd-dir-mode'];
        if (is_string($dirMode)) {
            $dirMode = octdec($dirMode);
        }

        if (!is_dir($webDir)) {
            echo 'The rapyd-web-dir (' . $webDir . ') specified in composer.json was not found in ' . getcwd() . ', can not install assets.' . PHP_EOL;

            return;
        }

        $targetDir = $webDir;

        $filesystem = new Filesystem();
        $filesystem->remove($targetDir);
        $filesystem->mkdir($targetDir, $dirMode);
        $filesystem->mkdir('test', $dirMode);
        //$filesystem->mkdir($targetDir, $dirMode);
        
        $filesystem->mirror(__DIR__ . '/../../../web', $targetDir);

        //$filesystem->mirror(__DIR__ . '/../../../classes/upload', $targetDir . '/classes/upload');
        //$filesystem->copy(__DIR__ . '/../../../app.php', $targetDir . '/app.php');
        //$filesystem->copy(__DIR__ . '/../../../classes/timthumb.php', $targetDir . '/classes/timthumb.php');

    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
            'rapyd-web-dir' => 'web',
            'rapyd-dir-mode' => 0777
        ), $event->getComposer()->getPackage()->getExtra());

        return $options;
    }
}