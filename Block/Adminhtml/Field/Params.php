<?php
namespace SableSoft\Smsp\Block\Adminhtml\Field;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\AbstractBlock;
use SableSoft\Smsp\Data\Form\Element\Textarea;

/**
 * Class Params
 * @package SableSoft\Smsp\Block\Adminhtml\Field
 */
class Params extends AbstractBlock {

    /** @var Textarea  */
    protected $element;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Textarea $element,
        array $data = []
    ) {
        parent::__construct( $context, $data );
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function _toHtml() {
        $this->element->setData( $this->getData() );
        return $this->element->getElementHtml();
    }
}
