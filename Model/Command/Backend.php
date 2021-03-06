<?php
namespace SableSoft\Smsp\Model\Command;

use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Serialize\SerializerInterface;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * Class Backend
 * @package SableSoft\Smsp\Model\Command
 */
class Backend extends ConfigValue {

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * ShippingMethods constructor
     *
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct( $context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data );
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave() {
        /** @var array $value */
        $value = $this->getValue();
        unset($value['__empty']);

        // validate commands before save:
        if( !$this->_validate( $value ) ) {
            $this->_dataSaveAllowed = false;
            return;
        }

        $encodedValue = $this->serializer->serialize( $value );

        $this->setValue( $encodedValue );
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad() {
        /** @var string $value */
        $value = $this->getValue();
        $decodedValue = !empty( $value ) ?
            $this->serializer->unserialize( $value ) : $value;

        $this->setValue( $decodedValue );
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function _validate( $data ) {
        foreach( $data as $row )
            if( !isset( $row['name'] ) || !isset( $row['params'] ) )
                return false;

        return true;
    }
}
