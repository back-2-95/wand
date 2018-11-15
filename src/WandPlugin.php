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
    static $webroot = 'public';

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
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
        ];
    }

    public function onPostInstall(Event $event)
    {
        $this->io->write('<comment>onPostInstall</comment>');
    }

    public function onPostUpdate(Event $event)
    {
        if (!self::$once) {
            $this->io->write('<comment>onPostUpdate</comment>');
            self::$once = true;
        }
        else {
            $this->io->write('<comment>onPostUpdate jo toisen kerran!</comment>');
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

    /**
     * Get absolute path to Drupal webroot.
     *
     * @param $project_root
     * @return string Absolute path to webroot.
     */
    protected static function getDrupalRoot($project_root) : string {
        return $project_root . DIRECTORY_SEPARATOR . self::$webroot;
    }
}
