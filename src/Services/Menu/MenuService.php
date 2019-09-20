<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 30.12.2017
 * Time: 23:08
 */

namespace Jinya\Services\Menu;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Menu\MenuDeleteEvent;
use Jinya\Framework\Events\Menu\MenuFillFromArrayEvent;
use Jinya\Framework\Events\Menu\MenuGetEvent;
use Jinya\Framework\Events\Menu\MenuSaveEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuService implements MenuServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MenuService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function saveOrUpdate(Menu $menu): Menu
    {
        $pre = $this->eventDispatcher->dispatch(new MenuSaveEvent($menu), MenuSaveEvent::PRE_SAVE);

        if (!$pre->isCancel()) {
            if (null === $menu->getId()) {
                $this->entityManager->persist($menu);
            }

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new MenuSaveEvent($menu), MenuSaveEvent::POST_SAVE);
        }

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        $this->eventDispatcher->dispatch(new ListEvent('', []), ListEvent::MENU_PRE_GET_ALL);

        $items = $this->entityManager->getRepository(Menu::class)->findAll();

        $this->eventDispatcher->dispatch(new ListEvent('', $items), ListEvent::MENU_POST_GET_ALL);

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): void
    {
        $pre = $this->eventDispatcher->dispatch(new MenuDeleteEvent($id), MenuDeleteEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $menu = $this->get($id);
            $this->entityManager->remove($menu);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new MenuDeleteEvent($id), MenuDeleteEvent::POST_DELETE);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): Menu
    {
        $this->eventDispatcher->dispatch(new MenuGetEvent($id, null), MenuGetEvent::PRE_GET);
        $menu = $this->entityManager->find(Menu::class, $id);
        $this->eventDispatcher->dispatch(new MenuGetEvent($id, $menu), MenuGetEvent::POST_GET);

        return $menu;
    }

    /**
     * Fills the menu items from the given array
     *
     * @param int $id
     * @param array $data
     */
    public function fillFromArray(int $id, array $data): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new MenuFillFromArrayEvent($id, $data),
            MenuFillFromArrayEvent::PRE_FILL_FROM_ARRAY
        );

        if (!$pre->isCancel()) {
            $menu = $this->get($id);
            $this->entityManager->transactional(function ($em) use ($data, $menu) {
                $menu->setMenuItems(new ArrayCollection());

                $menuItems = [];

                foreach ($data as $key => $item) {
                    if (0 === $item['nestingLevel']) {
                        $tail = array_slice($data, $key + 1, count($data));
                        $menuItem = $this->createSubmenu($item, $tail, $em);
                        $menuItem->setMenu($menu);
                        $menuItems[] = $menuItem;
                    }
                }

                $this->fixPositions($menuItems);
            });

            $this->entityManager->flush();
            $this->entityManager->refresh($menu);

            $this->eventDispatcher->dispatch(
                new MenuFillFromArrayEvent($id, $data),
                MenuFillFromArrayEvent::POST_FILL_FROM_ARRAY
            );
        }
    }

    private function createSubmenu(array $currentItem, array $tail, EntityManagerInterface $entityManager): MenuItem
    {
        $menuItem = MenuItem::fromArray($currentItem);

        $children = [];

        if (!empty($tail)) {
            $nestingLevel = $currentItem['nestingLevel'];

            foreach ($tail as $key => $item) {
                if ($nestingLevel + 1 === $item['nestingLevel']) {
                    $child = $this->createSubmenu($item, array_slice($tail, $key + 1, count($tail)), $entityManager);
                    $child->setParent($menuItem);
                    $entityManager->persist($child);
                    $children[] = $child;
                } elseif ($nestingLevel >= $item['nestingLevel']) {
                    break;
                }
            }
        }

        $children = $this->fixPositions($children);

        $menuItem->setChildren(new ArrayCollection($children));
        $entityManager->persist($menuItem);

        return $menuItem;
    }

    /**
     * @param array $items
     * @return array
     */
    private function fixPositions(array $items): array
    {
        $positionZero = array_filter($items, static function (MenuItem $item) {
            return 0 === $item->getPosition();
        });

        if (count($positionZero) === count($items)) {
            foreach ($items as $idx => $item) {
                /* @var MenuItem $item */
                $item->setPosition($idx);
            }
        }

        return $items;
    }
}
