<?php

namespace App\Web\Actions\Menu;

use App\Database\Menu;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllMenusAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Menu::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Menu::findAll()));
    }
}