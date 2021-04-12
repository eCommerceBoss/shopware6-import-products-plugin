<?php declare(strict_types=1);

namespace Sas\SyncerModule\Service;

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Context;

class SaveMedia
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * @var FileSaver
     */
    private $fileSaver;

    /**
     * ImageImport constructor.
     * @param MediaService $mediaService
     * @param FileSaver $fileSaver
     */
    public function __construct(MediaService $mediaService, FileSaver $fileSaver)
    {
        $this->mediaService = $mediaService;
        $this->fileSaver = $fileSaver;
    }


    public function addImageToProductMedia($image_content, Context $context)
    {
        $mediaId = NULL;

        $tempFile = tempnam(sys_get_temp_dir(), '');
        file_put_contents($tempFile, $image_content);
        $fileSize = filesize($tempFile);
        $mimeType = mime_content_type($tempFile);
        $fileExtension = "jpg";
        $unique_index = Uuid::randomHex();
        $actualFileName = "product_media_".$unique_index;
        $mediaFile = new MediaFile($tempFile, $mimeType, $fileExtension, $fileSize);
        $mediaId = $this->mediaService->createMediaInFolder('product', $context, false);

        $this->fileSaver->persistFileToMedia(
            $mediaFile,
            $actualFileName,
            $mediaId,
            $context
        );
        return $mediaId;
    }
}