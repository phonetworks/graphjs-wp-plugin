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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'GraphJS';

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $beforeWidget = $args['before_widget'];
        $title = $title ? $args['before_title'] . $title . $args['after_title'] : '';
        $afterWidget = $args['after_widget'];

        $widgetView = $this->wordpressPlugin->getPluginDirectory() . '/view/widget.php';
        include $widgetView;
    }
}
