<?php declare(strict_types=1);

namespace Shopware\User\Struct;

use Shopware\Locale\Struct\LocaleBasicStruct;
use Shopware\Media\Collection\MediaBasicCollection;

class UserDetailStruct extends UserBasicStruct
{
    /**
     * @var LocaleBasicStruct
     */
    protected $locale;

    /**
     * @var MediaBasicCollection
     */
    protected $media;

    public function __construct()
    {
        $this->media = new MediaBasicCollection();
    }

    public function getLocale(): LocaleBasicStruct
    {
        return $this->locale;
    }

    public function setLocale(LocaleBasicStruct $locale): void
    {
        $this->locale = $locale;
    }

    public function getMedia(): MediaBasicCollection
    {
        return $this->media;
    }

    public function setMedia(MediaBasicCollection $media): void
    {
        $this->media = $media;
    }
}