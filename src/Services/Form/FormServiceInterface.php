<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Jinya\Entity\Form\Form;

interface FormServiceInterface
{
    /**
     * Gets the specified @param string $slug
     * @see Form by slug
     */
    public function get(string $slug): Form;

    /**
     * Gets all entities by the given parameters
     *
     * @return Form[]
     */
    public function getAll(string $keyword = ''): array;

    /**
     * Counts all entities
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given @param Form $form
     * @see Form
     */
    public function saveOrUpdate(Form $form): Form;

    /**
     * Deletes the given @param Form $form
     * @see Form
     */
    public function delete(Form $form): void;
}
