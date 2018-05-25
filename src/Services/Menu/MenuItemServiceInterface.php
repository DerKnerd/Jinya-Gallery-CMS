<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 19:00.
 */

namespace Jinya\Services\Menu;

use Jinya\Entity\MenuItem;

interface MenuItemServiceInterface
{
    public const MENU = 'menu';
    public const PARENT = 'parent';

    /**
     * Gets the all menu items for the given menu.
     *
     * @param int    $parentId
     * @param string $type
     *
     * @return array
     */
    public function getAll(int $parentId, string $type = MenuItemServiceInterface::PARENT): array;

    /**
     * Gets the menu item by position and parent id.
     *
     * @param int    $parentId
     * @param int    $position
     * @param string $type
     *
     * @return MenuItem
     */
    public function get(int $parentId, int $position, string $type = MenuItemServiceInterface::PARENT): MenuItem;

    /**
     * Adds the given menu item.
     *
     * @param int      $parentId
     * @param MenuItem $item
     * @param string   $type
     *
     * @return void
     */
    public function addItem(int $parentId, MenuItem $item, string $type = MenuItemServiceInterface::PARENT): void;

    /**
     * Removes the given menu item from the menu.
     *
     * @param int    $id
     * @param int    $position
     * @param string $type
     */
    public function removeItem(int $id, int $position, string $type = MenuItemServiceInterface::PARENT): void;

    /**
     * Updates the given @see MenuItem.
     *
     * @param MenuItem $item
     *
     * @return MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem;
}
