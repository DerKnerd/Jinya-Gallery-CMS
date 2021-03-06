<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Database\Theme;
use App\Database\ThemeMenu;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/menu/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given theme menu')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('name', required: true, type: OpenApiParameter::TYPE_STRING)]
#[OpenApiRequestBody(['menu' => ['type' => 'integer']])]
#[OpenApiResponse('Successfully updated the theme menu', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Theme or menu not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme or menu not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class PutThemeMenuAction extends ThemeAction
{
    /**
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $menuId = $body['menu'];
        $menu = Menu::findById($menuId);
        if (!$menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $themeMenu = ThemeMenu::findByThemeAndName($themeId, $name);
        if (null !== $themeMenu) {
            $themeMenu->themeId = $themeId;
            $themeMenu->menuId = $menu->id;
            $themeMenu->name = $name;
            $themeMenu->update();
        } else {
            $themeMenu = new ThemeMenu();
            $themeMenu->themeId = $themeId;
            $themeMenu->menuId = $menu->id;
            $themeMenu->name = $name;
            $themeMenu->create();
        }

        return $this->noContent();
    }
}
