<?php

namespace Graphjs;

class Widget extends \WP_Widget
{
    private $wordpressPlugin;

    public function __construct(WordpressPlugin $wordpressPlugin)
    {
        $this->wordpressPlugin = $wordpressPlugin;
        $widgetOps = [
            'classname' => 'graphjs_widget',
        ];
        parent::__construct('graphjs_widget', 'GraphJS Widget', $widgetOps);
    }

    public function widget($args, $instance)
    {
        /**
         * @var \WP_Rewrite $wp_rewrite
         */
        global $wp_rewrite;
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'GraphJS';

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $beforeWidget = $args['before_widget'];
        $title = $title ? $args['before_title'] . $title . $args['after_title'] : '';
        $afterWidget = $args['after_widget'];

        $permalink = $wp_rewrite->get_page_permastruct();
        $permalink = str_replace('%pagename%', 'graphjs-login', $permalink);
        $loginUrl = home_url( user_trailingslashit($permalink, 'page'));

        $widgetView = $this->wordpressPlugin->getPluginDirectory() . '/view/widget.php';
        include $widgetView;
    }
}
