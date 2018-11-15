<?php

declare(strict_types=1);

namespace Druidfi\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Symfony\Component\Filesystem\Filesystem;

class WandPlugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;
    static $once = false;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        //$this->io->write('<comment>'. __METHOD__ .'</comment>');
        $fs = new Filesystem();
        $root = static::getDrupalRoot(getcwd());

        // Create the files directory with chmod 0777
        if (!$fs->exists($root . '/sites/default/files')) {
            $oldmask = umask(0);
            $fs->mkdir($root . '/sites/default/files', 0777);
            umask($oldmask);
            $this->io->write("Create a sites/default/files directory with chmod 0777");
        }
        else {
            $this->io->write("sites/default/files directory already exists");
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostInstall',
            //ScriptEvents::POST_AUTOLOAD_DUMP => 'onPostInstall',
            //PluginEvents::COMMAND => 'onPostInstall',
        ];
    }

    public function onPostInstall($event)
    {
        if (!self::$once) {
            $this->io->write('<comment>' . $event->getName() . '</comment>');
            self::$once = true;
        }
        else {
            $this->io->write('<comment>' . $event->getName() . ' jo toisen kerran!</comment>');
        }
        /*$query = [
            sprintf(
                "\n  <question>%s</question>\n",
                'What type of installation would you like?'
            ),
            "  [<comment>1</comment>] Minimal (no default middleware, templates, or assets; configuration only)\n",
            "  [<comment>2</comment>] Flat (flat source code structure; default selection)\n",
            "  [<comment>3</comment>] Modular (modular source code structure; recommended)\n",
            '  Make your selection <comment>(2)</comment>: ',
        ];

        $answer = $this->io->ask(implode($query), '2');

        $this->io->write('<error>You answered '. $answer .'</error>');*/
    }

    protected static function getDrupalRoot($project_root) {
        return $project_root . '/public';
    }
}
