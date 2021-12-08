<?php

namespace Firewards;

/**
 * @author            Dennis Stücken
 * @license           MIT
 * @copyright         firewards.com
 * @link              https://www.firewards.com
 *
 * @version           $Version$
 * @package           Firewards
 *
 */

namespace Firewards;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class GetRevueApi
{

    /**
     * GetRevue API Key
     *
     * @var string
     */
    protected $api_key;

    /**
     * Version of ConvertKit API
     *
     * @var string
     */
    protected $api_version = 'v2';

    /**
     * ConvertKit API URL
     *
     * @var string
     */
    protected $api_url_base = 'https://www.getrevue.co/api';

    /**
     * Debug
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     * Guzzle Http Client
     *
     * @var object
     */
    protected $client;

    /**
     * @var Response
     */
    protected $lastResponse;

    /**
     * @var Logger
     */
    protected $debugLogger;

    /**
     * @return Response
     */
    public function getLastResponse(): Response
    {
        return $this->lastResponse;
    }

    /**
     * GetRevueApi constructor.
     * @param $api_key
     * @param bool $debug
     * @param array $guzzleHttpOptions
     */
    public function __construct($api_key, $debug = false, $guzzleHttpOptions = ['verify' => false])
    {

        $this->api_key = $api_key;
        $this->debug = $debug;
        $this->client = new Client($guzzleHttpOptions);

        if ($debug) {
            $this->debugLogger = new Logger('revue-debug');
            $this->debugLogger->pushHandler(new StreamHandler(__DIR__ . '/../debug.log', Logger::DEBUG));
        }
    }

    private function logMessage($message)
    {
        if ($this->debug) {
            $this->debugLogger->info($message);
        }
    }

    /**
     * @return bool|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSubscribers()
    {
        return $this->request('subscribers', 'GET');
    }

    /**
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUnsubscribed()
    {
        return $this->request('subscribers/unsubscribed', 'GET');
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param bool $doubleOptIn
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addSubscriber(string $email, string $firstName = '', string $lastName = '', bool $doubleOptIn = false)
    {
        return $this->request('subscribers', 'POST', [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'double_opt_in' => $doubleOptIn,
        ]);
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSubscriber(string $email, string $firstName = '', string $lastName = '')
    {
        return $this->request('subscribers', 'PATCH', [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
    }


    /**
     * @param string $email
     * @param bool $member
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeMember(string $email, bool $member = true)
    {
        return $this->request('subscribers', 'PATCH', [
            'email' => $email,
            'member' => $member,
        ]);
    }

    /**
     * @param string $email
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribe(string $email)
    {
        return $this->request('subscribers/unsubscribe', 'POST', [
            'email' => $email,
        ]);
    }

    /**
     * @return bool|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLists()
    {
        return $this->request('lists', 'GET');
    }


    /**
     * Get the profile url
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountProfileUrl()
    {
        $request = $this->request('account/me', 'GET');
        return $request['profile_url'] ?? '';
    }

    /**
     * @param string $id
     * @return bool|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(string $id)
    {
        return $this->request('lists/' . $id, 'GET');
    }

    /**
     * @param $endpoint
     * @param $method
     * @param array $args
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($endpoint, $method, $args = array())
    {
        if (!is_string($endpoint) || !is_string($method) || !is_array($args)) {
            throw new \InvalidArgumentException;
        }

        $url = $this->api_url_base . '/' . $this->api_version . '/' . $endpoint;

        $this->logMessage(sprintf("Making request on %s.", $url));

        $request_body = json_encode($args);

        $this->logMessage(sprintf("%s, Request body: %s", $method, $request_body));

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $this->api_key,
        ];

        $options = [
            'exceptions' => false
        ];

        if ($method === "GET") {
            if ($args) {
                $url .= '?' . http_build_query($args);
            }
            $request = new Request($method, $url, $headers);
        } else {
            /*
            $options[RequestOptions::MULTIPART] = [];
            foreach ($args as $key => $val) {
                $options[RequestOptions::MULTIPART][] = [
                    'name' => $key,
                    'contents' => $val,
                ];
            }*/

            $headers['Content-Length'] = strlen($request_body);
            $request = new Request($method, $url, $headers, $request_body);
        }
        $this->lastResponse = $this->client->send($request, $options);

        $status_code = $this->lastResponse->getStatusCode();

        // If not between 200 and 300
        if (!preg_match("/^[2-3][0-9]{2}/", $status_code)) {
            $this->logMessage(sprintf("Response code is %s.", $status_code));

            return false;
        }

        $response_body = json_decode($this->lastResponse->getBody()->getContents(), true);

        if ($response_body) {
            $this->logMessage("Request successful.");
            return $response_body;
        }

        $this->logMessage("Request failed.");
        return false;

    }

}
