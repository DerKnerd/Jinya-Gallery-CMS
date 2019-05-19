<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 17:38
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\Menu\Menu;
use Jinya\Formatter\FormatterInterface;

interface MenuFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the @param Menu $menu
     * @return MenuFormatterInterface
     * @see MenuFormatterInterface
     *
     */
    public function init(Menu $menu): self;

    /**
     * Formats the name
     *
     * @return MenuFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the id
     *
     * @return MenuFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the logo
     *
     * @return MenuFormatterInterface
     */
    public function logo(): self;

    /**
     * Formats the items
     *
     * @return MenuFormatterInterface
     */
    public function items(): self;
}
