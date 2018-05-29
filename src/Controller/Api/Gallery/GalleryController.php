<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Entity\Gallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Gallery\GalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends BaseApiController
{
    /**
     * @Route("/api/gallery", methods={"GET"}, name="api_gallery_get_all")
     *
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAllAction(GalleryServiceInterface $galleryService, GalleryFormatterInterface $galleryFormatter): Response
    {
        return $this->getAllArt($galleryService, function ($gallery) use ($galleryFormatter) {
            return $galleryFormatter
                ->init($gallery)
                ->name()
                ->background()
                ->orientation()
                ->slug()
                ->description()
                ->format();
        });
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"GET"}, name="api_gallery_get")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(string $slug, GalleryServiceInterface $galleryService, GalleryFormatterInterface $galleryFormatter): Response
    {
        /* @noinspection PhpParamsInspection */
        return $this->getArt($slug, $galleryService, function ($gallery) use ($galleryFormatter) {
            $result = $galleryFormatter->init($gallery)
                ->name()
                ->slug()
                ->background()
                ->description()
                ->orientation();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->id()
                    ->created()
                    ->labels()
                    ->artworks();
            }

            return $result->format();
        });
    }

    /**
     * @Route("/api/gallery", methods={"POST"}, name="api_gallery_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function postAction(Request $request, GalleryServiceInterface $galleryService, GalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $galleryFormatter) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $slug = $this->getValue('slug', '');

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new Gallery();
            $gallery->setName($name);
            $gallery->setSlug($slug);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            return $galleryFormatter->init($galleryService->saveOrUpdate($gallery))
                ->name()
                ->slug()
                ->description()
                ->orientation()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"PUT"}, name="api_gallery_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function putAction(string $slug, Request $request, GalleryServiceInterface $galleryService, GalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $request, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($slug);

            $name = $this->getValue('name', $gallery->getName());
            $description = $this->getValue('description', $gallery->getDescription());
            $orientation = $this->getValue('orientation', $gallery->getOrientation());
            $slug = $this->getValue('slug', $gallery->getSlug());

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery->setName($name);
            $gallery->setSlug($slug);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            return $galleryFormatter->init($galleryService->saveOrUpdate($gallery))
                ->name()
                ->slug()
                ->description()
                ->orientation()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"DELETE"}, name="api_gallery_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(string $slug, GalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $mediaService) {
            $gallery = $galleryService->get($slug);
            if (!empty($gallery->getBackground())) {
                $mediaService->deleteMedia($gallery->getBackground());
            }

            $galleryService->delete($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
