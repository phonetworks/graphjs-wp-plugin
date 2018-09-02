<?php

namespace Graphjs;

class GraphjsApi
{
    const DEFAULT_BASE_URL = 'https://phonetworks.com';

    private $baseUrl;
    private $publicId;
    private $sessionCookie;

    public function __construct($config = [])
    {
        $this->baseUrl = $config['baseUrl'] ?? self::DEFAULT_BASE_URL;
        $this->publicId = $config['publicId'] ?? null;
    }

    public function setSessionCookie(\WP_Http_Cookie $sessionCookie = null)
    {
        $this->sessionCookie = $sessionCookie;
    }

    public function login($data)
    {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $queryParams = [
            'username' => rawurlencode($username),
            'password' => rawurlencode($password),
            'public_id' => rawurlencode($this->publicId),
        ];
        $url = $this->baseUrl . '/login';
        $url = add_query_arg($queryParams, $url);
        $response = wp_remote_get($url);

        return $response;
    }

    public function addPrivateContent($data)
    {
        $contentData = $data['data'];

        $queryParams = [
            'data' => rawurlencode($contentData),
            'public_id' => rawurlencode($this->publicId),
        ];
        $url = $this->baseUrl . '/addPrivateContent';
        $url = add_query_arg($queryParams, $url);
        $response = wp_remote_get($url, [
            'cookies' => [
                $this->sessionCookie,
            ],
        ]);

        return $response;
    }

    public function editPrivateContent($id, $data)
    {
        $contentData = $data['data'];

        $queryParams = [
            'id' => $id,
            'data' => rawurlencode($contentData),
            'public_id' => rawurlencode($this->publicId),
        ];
        $url = $this->baseUrl . '/editPrivateContent';
        $url = add_query_arg($queryParams, $url);
        $response = wp_remote_get($url, [
            'cookies' => [
                $this->sessionCookie,
            ],
        ]);

        return $response;
    }
}
