<?php

namespace Graphjs;

class GraphjsApi
{
    const BASE_URL = 'https://phonetworks.com';

    public function login($data)
    {
        $username = $data['username'];
        $password = $data['password'];

        $url = self::BASE_URL . '/login';
        $url = add_query_arg('username', $username, $url);
        $url = add_query_arg('password', $password, $url);

        return wp_remote_get($url);
    }

    public function getProfile($data)
    {
        $id = $data['id'];

        $url = self::BASE_URL . '/getProfile';
        $url = add_query_arg('id', $id, $url);

        return wp_remote_get($url);
    }
}
