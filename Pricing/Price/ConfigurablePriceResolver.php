<?php
namespace Jh\CoreBugOOSPriceFix\Pricing\Price;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Catalog\Model\Product;

/**
 * Class ConfigurablePriceResolver
 * @package Jh\CoreBugOOSPriceFix\Pricing\Price
 * @author Anthony Bates <anthony@wearejh.com>
 */
class ConfigurablePriceResolver implements PriceResolverInterface
{
    /** @var PriceResolverInterface */
    protected $priceResolver;

    /**
     * @var PriceCurrencyInterface
     * @deprecated
     */
    protected $priceCurrency;

    /**
     * @var Configurable
     * @deprecated
     */
    protected $configurable;

    /**
     * @var LowestPriceOptionsProviderInterface
     */
    private $lowestPriceOptionsProvider;

    /**
     * @param PriceResolverInterface              $priceResolver
     * @param Configurable                        $configurable
     * @param PriceCurrencyInterface              $priceCurrency
     * @param LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
     */
    public function __construct(
        PriceResolverInterface $priceResolver,
        Configurable $configurable,
        PriceCurrencyInterface $priceCurrency,
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider = null
    ) {
        $this->priceResolver = $priceResolver;
        $this->configurable = $configurable;
        $this->priceCurrency = $priceCurrency;
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider ?:
            ObjectManager::getInstance()->get(LowestPriceOptionsProviderInterface::class);
    }

    /**
     * @param SaleableInterface | Product $product
     *
     * @return float|null
     */
    public function resolvePrice(SaleableInterface $product)
    {
        $price = null;

        foreach ($this->lowestPriceOptionsProvider->getProducts($product) as $subProduct) {
            $productPrice = $this->priceResolver->resolvePrice($subProduct);
            $price = $price ? min($price, $productPrice) : $productPrice;
        }

        $price = $price ?: $product->getData('price');

        return $price === null ? null : (float)$price;
    }
}
