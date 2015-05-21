<?php

namespace RMP\Translate;

/**
 * Interface TranslateObserverInterface
 *
 * Allow logging of translation issues
 *
 * @package RMP\Translate
 * @author Programmes Developers <programmes-devel@lists.forge.bbc.co.uk>
 */

interface TranslateObserverInterface
{
    public function translateEventHandler(TranslateEvent $translateEvent);
}
