<?php
namespace Jh\CoreBugOOSPriceFix\Model\ResourceModel\Product;

use Magento\Catalog\Model\ResourceModel\Product\CompositeBaseSelectProcessor as MagentoCompositeBaseSelectProcessor;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\InputException;

/**
 * Class CompositeBaseSelectProcessor
 */
class CompositeBaseSelectProcessor extends MagentoCompositeBaseSelectProcessor implements BaseSelectProcessorInterface
{
    /**
     * @var BaseSelectProcessorInterface[]
     */
    private $baseSelectProcessors;

    /**
     * @param BaseSelectProcessorInterface[] $baseSelectProcessors
     * @throws InputException
     */
    public function __construct(
        array $baseSelectProcessors
    ) {
        foreach ($baseSelectProcessors as $baseSelectProcessor) {
            if (!$baseSelectProcessor instanceof BaseSelectProcessorInterface) {
                throw new InputException(
                    __("Processor %1 doesn't implement BaseSelectProcessorInterface", get_class($baseSelectProcessor))
                );
            }
        }
        $this->baseSelectProcessors = $baseSelectProcessors;
    }

    /**
     * @param Select $select
     * @return Select
     */
    public function process(Select $select)
    {
        unset($this->baseSelectProcessors['stock_status']);
        foreach ($this->baseSelectProcessors as $baseSelectProcessor) {
            $select = $baseSelectProcessor->process($select);
        }

        return $select;
    }
}
