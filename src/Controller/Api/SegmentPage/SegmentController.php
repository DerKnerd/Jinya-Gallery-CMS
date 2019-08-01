<?php

namespace Jinya\Controller\Api\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\SegmentPage\SegmentFormatterInterface;
use Jinya\Formatter\SegmentPage\SegmentPageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Jinya\Services\SegmentPages\SegmentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentController extends BaseApiController
{
    /**
     * @Route("/api/segment_page/{slug}/segment", methods={"GET"}, name="api_segment_page_segment_get")
     *
     * @param string $slug
     * @param SegmentPageServiceInterface $segmentPageService
     * @param SegmentPageFormatterInterface $segmentPageFormatter
     * @return Response
     */
    public function getAction(
        string $slug,
        SegmentPageServiceInterface $segmentPageService,
        SegmentPageFormatterInterface $segmentPageFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use (
            $slug,
            $segmentPageService,
            $segmentPageFormatter
        ) {
            $segmentPage = $segmentPageService->get($slug);

            return $segmentPageFormatter->init($segmentPage)->segments()->format()['segments'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment", methods={"POST"}, name="api_segment_page_segment_post")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param SegmentServiceInterface $segmentService
     * @param SegmentFormatterInterface $segmentFormatter
     * @return Response
     */
    public function postAction(
        string $slug,
        SegmentServiceInterface $segmentService,
        SegmentFormatterInterface $segmentFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $segmentService, $segmentFormatter) {
            $position = $this->getValue('position', 0);
            $artworkSlug = $this->getValue('artwork');
            $videoSlug = $this->getValue('video');
            $artGallerySlug = $this->getValue('artGallery');
            $videoGallerySlug = $this->getValue('videoGallery');
            $formSlug = $this->getValue('form');
            $html = $this->getValue('html');
            $action = $this->getValue('action', Segment::ACTION_NONE);
            $target = $this->getValue('target', '');
            $script = $this->getValue('script', '');

            if ($html !== null) {
                $segment = $segmentService->saveHtmlSegment($html, $slug, $position, $action, $target, $script);
            }

            if ($artworkSlug !== null) {
                $segment = $segmentService->saveArtworkSegment(
                    $artworkSlug,
                    $slug,
                    $position,
                    $action,
                    $target,
                    $script
                );
            }

            if ($videoSlug !== null) {
                $segment = $segmentService->saveVideoSegment($videoSlug, $slug, $position, $action, $target, $script);
            }

            if ($artGallerySlug !== null) {
                $segment = $segmentService->saveArtGallerySegment(
                    $artGallerySlug,
                    $slug,
                    $position,
                    $action,
                    $target,
                    $script
                );
            }

            if ($videoGallerySlug !== null) {
                $segment = $segmentService->saveVideoGallerySegment(
                    $videoGallerySlug,
                    $slug,
                    $position,
                    $action,
                    $target,
                    $script
                );
            }

            if ($formSlug !== null) {
                $segment = $segmentService->saveFormSegment($formSlug, $slug, $position, $action, $target, $script);
            }

            if (!isset($segment)) {
                throw new MissingFieldsException(['type' => 'api.segment_page.segment.field.type.missing']);
            }

            return $segmentFormatter
                ->init($segment)
                ->id()
                ->position()
                ->video()
                ->form()
                ->html()
                ->script()
                ->target()
                ->action()
                ->youtubeVideo()
                ->videoGallery()
                ->artGallery()
                ->artwork()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment/{id}", methods={"DELETE"}, name="api_segment_page_segment_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int id$
     * @param SegmentServiceInterface $segmentService
     * @return Response
     */
    public function deleteAction(int $id, SegmentServiceInterface $segmentService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $segmentService) {
            $segmentService->deleteSegment($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment/{id}", methods={"PUT"}, name="api_segment_page_segment_put")
     * @Route("/api/segment_page/{slug}/segment/{id}/{oldPosition}", methods={"PUT"}, name="api_segment_page_segment_put_with_position")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param int $oldPosition
     * @param string $slug
     * @param SegmentServiceInterface $segmentService
     * @return Response
     */
    public function putPositionAction(
        int $id,
        string $slug,
        SegmentServiceInterface $segmentService,
        int $oldPosition = -1
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($oldPosition, $id, $slug, $segmentService) {
            $position = $this->getValue('position', -1);
            $segment = $segmentService->get($id);

            $artworkSlug = $this->getValue('artwork');
            $videoSlug = $this->getValue('video');
            $artGallerySlug = $this->getValue('artGallery');
            $videoGallerySlug = $this->getValue('videoGallery');
            $formSlug = $this->getValue('form');
            $html = $this->getValue('html');
            $action = $this->getValue('action', $segment->getAction());
            $target = $this->getValue('target', $segment->getTarget());
            $script = $this->getValue('script', $segment->getScript());

            $segmentService->updateAction($id, $action, $target, $script);

            if ($position !== -1) {
                $segmentService->updatePosition($slug, $id, $oldPosition, $position);
            }

            if ($html !== null) {
                $segmentService->updateHtmlSegment($html, $id);
            }

            if ($artworkSlug !== null) {
                $segmentService->updateArtworkSegment($artworkSlug, $slug);
            }

            if ($videoSlug !== null) {
                $segmentService->updateVideoSegment($videoSlug, $slug);
            }

            if ($artGallerySlug !== null) {
                $segmentService->updateArtGallerySegment($artGallerySlug, $slug);
            }

            if ($videoGallerySlug !== null) {
                $segmentService->updateVideoGallerySegment($videoGallerySlug, $slug);
            }

            if ($formSlug !== null) {
                $segmentService->updateVideoGallerySegment($formSlug, $slug);
            }
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }
}