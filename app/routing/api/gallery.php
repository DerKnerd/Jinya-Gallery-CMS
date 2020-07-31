<?php

use App\Web\Actions\Gallery\CreateGalleryAction;
use App\Web\Actions\Gallery\DeleteGalleryAction;
use App\Web\Actions\Gallery\GetGalleryByIdAction;
use App\Web\Actions\Gallery\ListAllGalleriesAction;
use App\Web\Actions\Gallery\Positions\CreatePositionAction;
use App\Web\Actions\Gallery\Positions\DeletePositionAction;
use App\Web\Actions\Gallery\Positions\GetPositionsAction;
use App\Web\Actions\Gallery\Positions\UpdatePositionAction;
use App\Web\Actions\Gallery\UpdateGalleryAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('media/gallery', function (RouteCollectorProxy $group) {
        $group->get('', ListAllGalleriesAction::class);
        $group->post('', CreateGalleryAction::class)->add(new CheckRequiredFieldsMiddleware(['name']));
        $group->get('/{id}', GetGalleryByIdAction::class);
        $group->put('/{id}', UpdateGalleryAction::class);
        $group->delete('/{id}', DeleteGalleryAction::class);
        $group->group('/{galleryId}/file', function (RouteCollectorProxy $file) {
            $file->get('', GetPositionsAction::class);
            $file->post('', CreatePositionAction::class);
            $file->delete('/{position}', DeletePositionAction::class);
            $file->put('/{position}', UpdatePositionAction::class);
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};