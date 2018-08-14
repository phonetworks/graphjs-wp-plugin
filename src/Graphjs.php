<?php

namespace Graphjs;

use Graphjs\Markup\Element\Auth;

class Graphjs
{
    private $elements = [];

    public function __construct()
    {
        $this->_initElements();
    }

    private function _initElements()
    {
        $elements = [
            'auth',
            'auth-state',
            'auth-register',
            'auth-login',
            'auth-reset',
            'forum',
            'forum-list',
            'forum-thread',
            'forum-composer',
            'messages',
            'messages-composer',
            'profile',
            'profile-card',
            'profile-list',
            'group',
            'group-card',
            'group-list',
            'star-button',
            'star-list',
            'comments',
        ];
        $elementPrefix = 'graphjs-';

        $this->elements = array_map(function ($element) use ($elementPrefix) {
            return $elementPrefix . $element;
        }, $elements);
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }
}
