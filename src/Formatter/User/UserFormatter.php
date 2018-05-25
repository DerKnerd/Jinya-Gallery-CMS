<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:17
 */

namespace Jinya\Formatter\User;

use Jinya\Entity\Artwork;
use Jinya\Entity\Form;
use Jinya\Entity\Gallery;
use Jinya\Entity\Page;
use Jinya\Entity\User;
use Jinya\Formatter\Artwork\ArtworkFormatterInterface;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Gallery\GalleryFormatterInterface;
use Jinya\Formatter\Page\PageFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function array_map;

class UserFormatter implements UserFormatterInterface
{
    /** @var User */
    private $user;

    /** @var array */
    private $formattedData;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var GalleryFormatterInterface */
    private $galleryFormatter;

    /** @var ArtworkFormatterInterface */
    private $artworkFormatter;

    /** @var PageFormatterInterface */
    private $pageFormatter;

    /** @var FormFormatterInterface */
    private $formFormatter;

    /**
     * UserFormatter constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param GalleryFormatterInterface $galleryFormatter
     */
    public function setGalleryFormatter(GalleryFormatterInterface $galleryFormatter): void
    {
        $this->galleryFormatter = $galleryFormatter;
    }

    /**
     * @param ArtworkFormatterInterface $artworkFormatter
     */
    public function setArtworkFormatter(ArtworkFormatterInterface $artworkFormatter): void
    {
        $this->artworkFormatter = $artworkFormatter;
    }

    /**
     * @param PageFormatterInterface $pageFormatter
     */
    public function setPageFormatter(PageFormatterInterface $pageFormatter): void
    {
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @param FormFormatterInterface $formFormatter
     */
    public function setFormFormatter(FormFormatterInterface $formFormatter): void
    {
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatting
     *
     * @param User $user
     * @return UserFormatterInterface
     */
    public function init(User $user): UserFormatterInterface
    {
        $this->user = $user;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the lastname
     *
     * @return UserFormatterInterface
     */
    public function lastname(): UserFormatterInterface
    {
        $this->formattedData['lastname'] = $this->user->getLastname();

        return $this;
    }

    /**
     * Formats the roles
     *
     * @return UserFormatterInterface
     */
    public function roles(): UserFormatterInterface
    {
        $this->formattedData['roles'] = $this->user->getRoles();

        return $this;
    }

    /**
     * Formats the email
     *
     * @return UserFormatterInterface
     */
    public function email(): UserFormatterInterface
    {
        $this->formattedData['email'] = $this->user->getEmail();

        return $this;
    }

    /**
     * Formats the enable state
     *
     * @return UserFormatterInterface
     */
    public function enabled(): UserFormatterInterface
    {
        $this->formattedData['enabled'] = $this->user->isEnabled();

        return $this;
    }

    /**
     * Formats the profile picture
     *
     * @return UserFormatterInterface
     */
    public function profilePicture(): UserFormatterInterface
    {
        $this->formattedData['profilePicture'] = $this->urlGenerator->generate('api_user_profilepicture_get', ['id' => $this->user->getId()]);

        return $this;
    }

    /**
     * Shorthand for @see UserFormatterInterface::profilePicture(), @see UserFormatterInterface::firstname(), @see UserFormatterInterface::lastname(), @see UserFormatterInterface::email()
     *
     * @return UserFormatterInterface
     */
    public function profile(): UserFormatterInterface
    {
        return $this
            ->firstname()
            ->lastname()
            ->email()
            ->profilePicture();
    }

    /**
     * Formats the firstname
     *
     * @return UserFormatterInterface
     */
    public function firstname(): UserFormatterInterface
    {
        $this->formattedData['firstname'] = $this->user->getFirstname();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return UserFormatterInterface
     */
    public function id(): UserFormatterInterface
    {
        $this->formattedData['id'] = $this->user->getId();

        return $this;
    }

    /**
     * Formats the created artworks
     *
     * @return UserFormatterInterface
     */
    public function createdArtworks(): UserFormatterInterface
    {
        $this->formattedData['created']['artworks'] = array_map(function (Artwork $artwork) {
            return $this->artworkFormatter
                ->init($artwork)
                ->name()
                ->slug()
                ->description()
                ->picture()
                ->format();
        }, $this->user->getCreatedArtworks()->toArray());

        return $this;
    }

    /**
     * Formats the created galleries
     *
     * @return UserFormatterInterface
     */
    public function createdGalleries(): UserFormatterInterface
    {
        $this->formattedData['created']['galleries'] = array_map(function (Gallery $gallery) {
            return $this->galleryFormatter
                ->init($gallery)
                ->name()
                ->slug()
                ->description()
                ->background()
                ->format();
        }, $this->user->getCreatedGalleries()->toArray());

        return $this;
    }

    /**
     * Formats the created pages
     *
     * @return UserFormatterInterface
     */
    public function createdPages(): UserFormatterInterface
    {
        $this->formattedData['created']['pages'] = array_map(function (Page $page) {
            return $this->pageFormatter
                ->init($page)
                ->name()
                ->slug()
                ->format();
        }, $this->user->getCreatedPages()->toArray());

        return $this;
    }

    /**
     * Formats the created forms
     *
     * @return UserFormatterInterface
     */
    public function createdForms(): UserFormatterInterface
    {
        $this->formattedData['created']['forms'] = array_map(function (Form $form) {
            return $this->formFormatter
                ->init($form)
                ->name()
                ->slug()
                ->format();
        }, $this->user->getCreatedForms()->toArray());

        return $this;
    }
}
