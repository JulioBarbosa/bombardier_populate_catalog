<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Model\Api;

use Exception;
use Magento\Framework\Exception\CronException;
use Magento\Framework\HTTP\Client\Curl;
use function __;
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
     * @return mixed
     * @throws CronException
     */
    public function fetchData($url)
    {
        try {
            $this->curlClient->get($url);
            return json_decode($this->curlClient->getBody(), true);
        } catch (Exception $exception) {
            throw new CronException(__($exception->getMessage()), $exception);
        }
    }
}
