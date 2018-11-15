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

class WandPlugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->io->write('<comment>'. __METHOD__ .'</comment>');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_UPDATE_CMD => 'onPostInstall',
            //PluginEvents::COMMAND => 'onPostInstall',
        ];
    }

    public function onPostInstall($event)
    {
        $this->io->write('<comment>'. __METHOD__ .' '. __CLASS__. '</comment>');

        $query = [
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

        $this->io->write('<error>You answered '. $answer .'</error>');
    }
}
