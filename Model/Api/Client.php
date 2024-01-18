<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace Bombardier\PopulateCatalog\Model\Api;

use Exception;
use Magento\Framework\HTTP\Client\Curl;
use function json_decode;

class Client
{
    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->curlClient = $curl;
    }

    /**
     * Request to get data
     *
     * @param $url
     * @return mixed|void
     */
    public function fetchData($url)
    {
        try {
            $this->curlClient->get($url);
            return json_decode($this->curlClient->getBody(), true);
        } catch (Exception $exception) {

        }
    }
}
