<?php
namespace SableSoft\Smsp\Data\Form\Element;

/**
 * Class Textarea
 * @package SableSoft\Smsp\Data\Form\Element
 */
class Textarea extends \Magento\Framework\Data\Form\Element\Textarea {

    /**
     * @return string
     */
    public function getElementHtml() {
        $html = '<textarea id="' . $this->getData('input_id') .
            '" name="' . $this->getData('input_name') . '" '
            . $this->serialize($this->getHtmlAttributes()) . ' >';
        $html .= $this->getEscapedValue();
        $html .= "</textarea>";
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @param null $index
     * @return string
     */
    public function getEscapedValue( $index = null ) {
        return "<%- " . $this->getData('column_name') . " %>";
    }
}
