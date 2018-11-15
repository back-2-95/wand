<?php

declare(strict_types=1);

namespace Druidfi\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class WandPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;

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

        $answer = $io->ask(implode($query), '2');

        $io->write('<error>You answered '. $answer .'</error>');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [];
    }
}
