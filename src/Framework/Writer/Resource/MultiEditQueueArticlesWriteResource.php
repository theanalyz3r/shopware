<?php declare(strict_types=1);

namespace Shopware\Framework\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\MultiEditQueueArticlesWrittenEvent;
use Shopware\Framework\Write\WriteResource;

class MultiEditQueueArticlesWriteResource extends WriteResource
{
    public function __construct()
    {
        parent::__construct('s_multi_edit_queue_articles');
    }

    public function getWriteOrder(): array
    {
        return [
            self::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $rawData = [], array $errors = []): MultiEditQueueArticlesWrittenEvent
    {
        $event = new MultiEditQueueArticlesWrittenEvent($updates[self::class] ?? [], $context, $rawData, $errors);

        unset($updates[self::class]);

        /**
         * @var WriteResource
         * @var string[]      $identifiers
         */
        foreach ($updates as $class => $identifiers) {
            $event->addEvent($class::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}