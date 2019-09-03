<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Form\Form;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Form\FormEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormService implements FormServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * FormService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugServiceInterface $slugService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Form::class);
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the specified @param string $slug
     * @return Form
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see Form by slug
     */
    public function get(string $slug): Form
    {
        $this->eventDispatcher->dispatch(new FormEvent(null, $slug), FormEvent::PRE_GET);

        $form = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(new FormEvent($form, $slug), FormEvent::POST_GET);

        return $form;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Form[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(new ListEvent($offset, $count, $keyword, []), ListEvent::FORMS_PRE_GET_ALL);

        $items = $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('form')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            new ListEvent($offset, $count, $keyword, $items),
            ListEvent::FORMS_POST_GET_ALL
        );

        return $items;
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Form::class, 'form')
            ->where('form.title LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::FORMS_PRE_COUNT);

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(form)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::FORMS_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given @param Form $form
     * @return Form
     * @throws EmptySlugException
     * @see Form
     */
    public function saveOrUpdate(Form $form): Form
    {
        $pre = $this->eventDispatcher->dispatch(new FormEvent($form, $form->getSlug()), FormEvent::PRE_SAVE);

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($form);
            $this->eventDispatcher->dispatch(new FormEvent($form, $form->getSlug()), FormEvent::POST_SAVE);
        }

        return $form;
    }

    /**
     * Deletes the given @param Form $form
     * @see Form
     */
    public function delete(Form $form): void
    {
        $pre = $this->eventDispatcher->dispatch(new FormEvent($form, $form->getSlug()), FormEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $this->baseService->delete($form);
            $this->eventDispatcher->dispatch(new FormEvent($form, $form->getSlug()), FormEvent::POST_DELETE);
        }
    }
}
