<?php
namespace SableSoft\Smsp\Model;

use SableSoft\Smsp\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;

/**
 * Class Api
 *
 * @package SableSoft\Smsp\Model
 */
class Api {
    /** @var bool - ready for use flag */
    private $ready;
    /** @var string */
    private $url;
    /** @var array - request params */
    private $params = [];
    /** @var array - required api params */
    protected $required = [
        'url'       => Data::TYPE_URL,
        'user'      => Data::TYPE_EMAIL,
        'apikey'    => Data::TYPE_STRING,
        'devkey'    => Data::TYPE_STRING
    ];
    /** @var Curl  */
    protected $curl;
    /** @var Data  */
    protected $helper;
    /** @var Config  */
    protected $config;
    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    /** @var array - smsp.by api commands with params for use @todo - move to config! */
    protected $commands = [
        'msg_send' => [
            'recipients'    => '!numbers',
            'urgent'        => Data::TYPE_FLAG
        ]
    ];

    /** @var array - curl options */
    protected $options = [];
    /** @var array - curl response cache */
    public $response;
    /** @var string  */
    public $error;

    /**
     * Api constructor.
     * @param Curl $curl
     * @param Config $config
     */
    public function __construct(
        Curl $curl,
        Data $helper,
        Config $config,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->config = $config;
        $this->logger = $logger;
        // set ready flag:
        $this->ready = $this->isReady();
    }

    /**
     * @param string $command
     * @param array $params
     * @return bool
     */
    public function request( string $command, $params = [] ) : bool {
        // check api is ready for use:
        if( !$this->isReady() ) {
            $this->error = __( 'Smsp api not ready for requesting!' );
            $this->logger->warning( $this->error );

            return false;
        }

        // validate and prepare request data:
        if( !$this->prepare( $command, (array) $params ) )
            return false;

        try {
            // send request:
            $this->curl->post( $this->url, $this->getParams() );
            // caching response:
            $this->response = json_decode( $this->curl->getBody(), true );
        } catch ( \Exception $e ) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getParams() : array {
        return $this->params;
    }

    /**
     * Clean response and keep only required params
     */
    public function clean() : void {
        $required = [];
        foreach( $this->required as $key => $type )
            if( isset( $this->params[ $key ] ) )
                $required[ $key ] = $this->params[ $key ];
        $this->params = $required;
        // clean old response cache:
        $this->response = null;
    }

    /**
     * @return bool
     */
    public function isReady() : bool {
        if( !is_null( $this->ready ) )
            return $this->ready;

        // check required api params:
        foreach( $this->required as $key => $type ):
            $value = $this->config->getValue( $key );
            if( empty( $value ) ) {
                $this->error = __( "Required smsp.by api parameter missing: '%1'. Please check you module configs!", $key );
                $this->logger->warning( $this->error );

                return false;
            }
            if( !$this->helper->validate( $value, $type ) ) {
                $this->error = __( "Required smsp.by api parameter not valid: '%1'. Please check you module configs!", $key );
                $this->logger->warning( $this->error );

                return false;
            }
            // set required param:
            if( $key === 'url' ) {
                $this->url = $value;
            } else
                $this->params[ $key ] = $value;
        endforeach;

        return true;
    }

    /**
     * @param string $command
     * @param array $params
     * @return bool
     */
    protected function prepare( string $command, array $params = [] ) : bool {
        // clean old params before:
        $this->clean();
        // get and check command config:
        $config = $this->config->getCommandParams( $command );
        if( $config === false ) {
            $this->error = __( "Invalid smsp api command: '%1'", $command );
            $this->logger->warning( $this->error );

            return false;
        }

        // check all available params for command:
        foreach( (array) $config as $param => $type ):
            // check is required:
            $required = false;
            if( $type[0] === '!' ) {
                $required = true;
                $type = substr( $type, 1 );
            }
            if( $required && !isset( $params[ $param ] ) ) {
                $this->error = __( "Parameter for command '%1' is required: '%2'", $command, $param );
                $this->logger->warning( $this->error );

                return false;
            }
            // prepare param:
            $this->prepareParam( $param, $type, $params );
            // validate and prepare param if exist:
            if( isset( $params[ $param ] ) ) {
                $value = $params[ $param ];
                if( $this->helper->validate( $value, $type ) ) {
                    $this->params[ $param ] = $value;
                } else
                    $this->logger->warning( __( "Invalid value for parameter: '%1'. Skipping...", $param ) );
            }

        endforeach;
        // set api command param:
        $this->params['r'] = 'api' . DIRECTORY_SEPARATOR . $command;

        return true;
    }

    /**
     * @param string $param
     * @param string $type
     * @param array $params
     */
    protected function prepareParam( string $param, string $type, array &$params ) {
        if( $param === Config::FIELD_SENDER && !isset( $params[ $param ] ) )
            $params[ $param ] = $this->config->getValue( Config::FIELD_SENDER );

        if( !isset( $params[ $param ] ) )
            return;

        $value = $params[ $param ];
        $code = $this->config->getValue( Config::FIELD_COUNTRY );
        if( $type === Data::TYPE_PHONE )
            $params[ $param ] = $code . $value;

        if( $type === Data::TYPE_PHONES ) {
            $array = [];
            foreach( (array) $value as $v )
                $array[] = $code . $v;
            $params[ $param ] = $array;
        }
    }
}
