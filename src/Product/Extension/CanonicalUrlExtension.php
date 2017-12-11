<?php declare(strict_types=1);

namespace Shopware\Product\Extension;

use Shopware\Api\Entity\EntityExtensionInterface;
use Shopware\Api\Entity\Field\ManyToOneAssociationField;
use Shopware\Api\Entity\FieldCollection;
use Shopware\Api\Search\Criteria;
use Shopware\Api\Search\Query\TermQuery;
use Shopware\Api\Search\Query\TermsQuery;
use Shopware\Api\Write\Flag\Deferred;
use Shopware\Api\Write\Flag\Extension;
use Shopware\Product\Definition\ProductDefinition;
use Shopware\Product\Event\Product\ProductBasicLoadedEvent;
use Shopware\Seo\Definition\SeoUrlDefinition;
use Shopware\Seo\Repository\SeoUrlRepository;
use Shopware\Storefront\Page\Detail\DetailPageUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CanonicalUrlExtension implements EntityExtensionInterface, EventSubscriberInterface
{
    /**
     * @var SeoUrlRepository
     */
    private $seoUrlRepository;

    public function __construct(SeoUrlRepository $seoUrlRepository)
    {
        $this->seoUrlRepository = $seoUrlRepository;
    }

    public function extendFields(FieldCollection $collection)
    {
        $collection->add(
            (new ManyToOneAssociationField('canonicalUrl', 'uuid', SeoUrlDefinition::class, true, 'foreign_key'))->setFlags(new Extension(), new Deferred())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductBasicLoadedEvent::NAME => 'productBasicLoaded',
        ];
    }

    public function productBasicLoaded(ProductBasicLoadedEvent $event)
    {
        if ($event->getProducts()->count() <= 0) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new TermQuery('seo_url.name', DetailPageUrlGenerator::ROUTE_NAME));
        $criteria->addFilter(new TermsQuery('seo_url.foreignKey', $event->getProducts()->getUuids()));
        $criteria->addFilter(new TermQuery('seo_url.isCanonical', 1));

        $urls = $this->seoUrlRepository->search($criteria, $event->getContext());

        foreach ($urls as $url) {
            $product = $event->getProducts()->get($url->getForeignKey());
            $product->addExtension('canonicalUrl', $url);
        }
    }
}