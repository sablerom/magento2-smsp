<?php
namespace SableSoft\Smsp\Model\Command;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class CommandConfig
 * @package SableSoft\Smsp\Model\Command
 */
class Frontend extends AbstractFieldArray {

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender() {

        $this->addColumn('name',
            [
                'label' => __('Command Name'),
                'class' => 'required-entry'
            ]);
        $this->addColumn('params',
            [
                'label'     => __('Command Params'),
                'class'     => 'required-entry',
                'renderer'  => $this->getLayout()->createBlock(
                    '\SableSoft\Smsp\Block\Adminhtml\Field\Params'
                )
            ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Command');
    }
}
