<?php

namespace Repay\Modal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void open(string $component, array $params = [])
 * @method static void close(string $modalId)
 * @method static void closeAll()
 * @method static bool hasOpenModals()
 * @method static int getModalDepth()
 * @method static array getModalStack()
 *
 * @see \Repay\Modal\ModalManager
 */
class Modal extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'repay.modal';
    }
}
