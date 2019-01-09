<?php
namespace SableSoft\Smsp\Controller\Index;

/**
 * Class Command
 * @package SableSoft\Smsp\Controller\Index
 */
class Command extends Index {

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        $config = $this->command ?
            $this->config->getCommandParams( $this->command ) :
            $this->config->getCommands();

        return $this->response([
            'command' => $this->command,
            'config'  => $config
        ]);
    }
}
