<?php
namespace SableSoft\Smsp\Controller\Index;

use SableSoft\Smsp\Model\Api;
use SableSoft\Smsp\Model\Config;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 * @package SableSoft\Smsp\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action {

    const PARAMS    = 'params';
    const COMMAND   = 'command';

     /** @var Api */
    protected $api;
    /** @var string */
    protected $command;
    /** @var string */
    protected $params;
    /** @var Config  */
    protected $config;

    /**
     * Index constructor.
     * @param Api $api
     * @param Context $context
     */
    public function __construct(
        Api $api,
        Config $config,
        Context $context
    ) {
        parent::__construct( $context );
        // prepare request data:
        $this->prepare();
        $this->api = $api;
        $this->config = $config;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch( RequestInterface $request ) {
        if( !$this->access( $request ) )
            return $this->redirect();

        return parent::dispatch( $request );
    }

    /**
     * Make smps.by api request
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        if( !$this->command )  {
            $data = [
                'success'   => false,
                'error'     => __( "Request command not found!" )
            ];
        } else {
            try {
                $success = $this->api->request( $this->command, $this->params );
                $data = [
                    'success'     => $success,
                    'error'       => $this->api->error,
                    'command'     => $this->command,
                    'params'      => $this->params,
                    'response'    => $this->api->response
                ];
                //set request full params if develop:
                if( $this->config->getValue('is_develop') )
                    $data['request'] = $this->api->getParams();
            } catch( \Exception $e ) {
                $data = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $this->response( $data );
    }

    /**
     * Access checking @todo
     *
     * @param Http|RequestInterface $request
     * @return bool
     */
    protected function access( $request ) {
        if( $this->config->getValue( Config::FIELD_DEVELOP ) )
            return true;

        return $request->isAjax();
    }

    /**
     * Prepare request data
     */
    protected function prepare() {
        $this->command = $this->_request->getParam( self::COMMAND );
        $this->params = $this->_request->getParam( self::PARAMS );
    }

    /**
     * @param array $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function response( array $data ) {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(
            ResultFactory::TYPE_JSON
        );

        return $result->setData( $data );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function redirect() {
        /** @var \Magento\Framework\Controller\Result\Redirect $redirect */
        $redirect = $this->resultFactory->create( ResultFactory::TYPE_REDIRECT );
        $redirect->setUrl('/');

        return $redirect;
    }
}
