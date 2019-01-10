<?php
namespace SableSoft\Smsp\Model;

use SableSoft\Core\Model\Config as CoreConfig;

/**
 * Class SmspConfig
 *
 * @package SableSoft\Smsp\Model
 */
class Config extends CoreConfig {
    /** @var string  */
    const SECTION           = 'smsp';

    const FIELD_USER        = 'user';
    const FIELD_KEY         = 'apikey';
    const FIELD_SENDER      = 'sender';
    const FIELD_COUNTRY     = 'country';

    const FIELD_URL         = 'url';
    const FIELD_DEVKEY      = 'devkey';
    const FIELD_COMMANDS    = 'commands';

    protected $section = self::SECTION;
    /** @var array */
    protected $commands;

    /** @var array - settings keys */
    protected $keys = [
        // general settings:
        self::FIELD_USER      => self::GROUP_GENERAL,
        self::FIELD_KEY       => self::GROUP_GENERAL,
        self::FIELD_SENDER    => self::GROUP_GENERAL,
        self::FIELD_COUNTRY   => self::GROUP_GENERAL,
        self::FIELD_DEVELOP   => self::GROUP_GENERAL,
        // dev settings:
        self::FIELD_URL       => self::GROUP_DEVELOP,
        self::FIELD_DEVKEY    => self::GROUP_DEVELOP,
        self::FIELD_COMMANDS  => self::GROUP_DEVELOP
    ];

    public function getCommands() {
        $commands = [];
        $value = $this->getValue( self::FIELD_COMMANDS );
        $array = $this->serializer->unserialize( $value );
        foreach( $array as $i => $config )
            $commands[ $config['name'] ] = $config['params'];

        return $commands;
    }

    /**
     * @param string|null $name
     * @return array|bool
     */
    public function getCommandParams( string $name = null ) {
        $commands = $this->getCommands();
        if( !array_key_exists( $name, $commands ) ) {
            $this->logger->warning( __( "Unknown smsp command name: '%1'", $name ) );

            return false;
        }
        $params = [];
        // get command config string:
        $raw = $commands[ $name ];
        if( !empty( $raw ) ) {
            $rows = explode(',', $raw );
            foreach( $rows as $row ) {
                $data = explode( ':', $row );
                $param = trim( $data[0] );
                $type = trim( $data[1] );
                $params[ $param ] = $type;
            }
        }

        return $params;
    }
}
