<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:22
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Component\EventDispatcher\Event;

class CancellableEvent extends Event
{
    /** @var bool */
    private $cancel = false;

    /**
     * @return bool
     */
    public function isCancel(): bool
    {
        return $this->cancel;
    }

    /**
     * @param bool $cancel
     */
    public function setCancel(bool $cancel): void
    {
        if ($cancel) {
            $this->stopPropagation();
        }

        $this->cancel = $cancel;
    }
}
