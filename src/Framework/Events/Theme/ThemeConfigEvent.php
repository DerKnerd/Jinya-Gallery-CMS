<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 00:50
 */

namespace Jinya\Framework\Events\Theme;

use Jinya\Framework\Events\Common\CancellableEvent;

class ThemeConfigEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ThemeConfigPreSave';

    public const POST_SAVE = 'ThemeConfigPostSave';

    public const PRE_RESET = 'ThemeConfigPreReset';

    public const POST_RESET = 'ThemeConfigPostReset';

    /** @var string */
    private string $themeName;

    /** @var array */
    private array $config;

    /** @var bool */
    private bool $override;

    /**
     * ThemeConfigEvent constructor.
     * @param string $themeName
     * @param array $config
     * @param bool $override
     */
    public function __construct(string $themeName, array $config, bool $override)
    {
        $this->themeName = $themeName;
        $this->config = $config;
        $this->override = $override;
    }

    /**
     * @return string
     */
    public function getThemeName(): string
    {
        return $this->themeName;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isOverride(): bool
    {
        return $this->override;
    }
}
