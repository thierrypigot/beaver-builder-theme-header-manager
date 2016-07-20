<?php
/**
 * Plugin Name: BB theme : Header manager
 * Plugin URI: http://www.thierry-pigot.fr
 * Description: Header manager for Beaver Builder Theme.
 * Version: 1.0
 * Author: Thierry Pigot
 * Author URI: http://www.thierry-pigot.fr
 *
 */

add_filter('fl_builder_register_settings_form', 'tp_filter_settings_form', 10, 2);
function tp_filter_settings_form($form, $id)
{
    if ('layout' == $id) {
        // Modify the form array however we want
        $css3 = array(
            'title' => __('Options', 'fl-builder'),
            'sections' => array(
                'headerpage' => array(
                    'title' => '',
                    'fields' => array(
                        'header_style' => array(
                            'label' => __('Header Style', 'fl-builder'),
                            'type' => 'select',
                            'options' => array(
                                '' => 'Default',
                                'hidden' => __('Hide Header', 'fl-builder'),
                                'overlay' => __('Overlay Header', 'fl-builder'),
                            ),
							'preview'       => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
							),
                            'toggle' => array(
                                'overlay' => array(
                                    'fields' => array('bg_color', 'bg_opacity', 'border', 'shadow', 'hidden', 'height')
                                )
                            )
                        ),
                        'bg_color' => array(
                            'type' => 'color',
                            'label' => __('Background color', 'fl-builder'),
                            'default' => '#333',
                            'show_reset' => true,
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        ),
                        'bg_opacity' => array(
                            'type' => 'text',
                            'label' => __('Background Opacity', 'fl-builder'),
                            'default' => '100',
                            'description' => '%',
                            'maxlength' => '3',
                            'size' => '5',
                            'placeholder' => '100',
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        ),
                        'height' => array(
                            'type' => 'text',
                            'label' => __('Height to change opacity', 'fl-builder'),
                            'default' => '100',
                            'maxlength' => '3',
                            'size' => '5',
                            'placeholder' => '100',
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        ),
                        'border' => array(
                            'type' => 'select',
                            'label' => __('Border bottom', 'fl-builder'),
                            'default' => 'yes',
                            'options' => array(
                                'yes' => __('Yes', 'fl-builder'),
                                'no' => __('No', 'fl-builder')
                            ),
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        ),
                        'shadow' => array(
                            'type' => 'select',
                            'label' => __('Shadow', 'fl-builder'),
                            'default' => 'no',
                            'options' => array(
                                'yes' => __('Yes', 'fl-builder'),
                                'no' => __('No', 'fl-builder')
                            ),
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        ),
                        'hidden' => array(
                            'type' => 'select',
                            'label' => __('Hide on start', 'fl-builder'),
                            'default' => 'no',
                            'options' => array(
                                'yes' => __('Yes', 'fl-builder'),
                                'no' => __('No', 'fl-builder')
                            ),
                            'preview' => array(
								'type'          => 'css',
								'selector'      => 'header.fl-page-header',
                            )
                        )
                    )
                )
            )
        );
        $form['tabs']['css3'] = $css3;
    }
    return $form;
}


// Add <body> class based on setting
add_filter('body_class', 'tp_filter_body_class');
function tp_filter_body_class($classes)
{
    $settings = FLBuilderModel::get_layout_settings();
    if ($settings->header_style) {
        $style = $settings->header_style;
        $classes[] = "header-style-$style";
    }
    return $classes;
}


// Add CSS Styles to this page's stylesheet
add_filter('fl_builder_render_css', 'tp_filter_render_css', 10, 3);
function tp_filter_render_css($css, $nodes, $global_settings)
{
    $settings = FLBuilderModel::get_layout_settings();
    if ('overlay' == $settings->header_style) {

        $css .= '@media only screen and (min-width : 970px) {';

        if ('no' == $settings->border)
            $css .= '.fl-page-header-wrap{border: none !important;}';

        if ('yes' == $settings->shadow)
            $css .= 'header.fl-page-header{box-shadow: 0 1px 12px 0px rgba(51, 51, 51, 0.23); -webkit-box-shadow: 0 1px 12px 0px rgba(51, 51, 51, 0.23);}';

        if ($settings->bg_opacity < 100) {
            $clr = "rgba(" . implode(',', FLBuilderColor::hex_to_rgb($settings->bg_color)) . ", " . $settings->bg_opacity / 100 . ")";
        } else {
            $clr = "rgba(" . implode(',', FLBuilderColor::hex_to_rgb($settings->bg_color)) . ", 1)";
        }
        $css .= 'header.fl-page-header { background: ' . $clr . ' !important } .fl-page-header-wrap{ background: ' . $clr . '; }';

        $css .= 'header.fl-page-header { position: fixed; top: 0; left: 0; right:0; z-index: 9999; } .fl-page-content {z-index: 0;}';
        $css .= 'body.admin-bar header.fl-page-header { top: 32px; }';
        $css .= 'body.scrolling header.fl-page-header { background: #' . $settings->bg_color . '; -webkit-transition: background-color .5s linear; -moz-transition: background-color .5s linear; -o-transition: background-color .5s linear; -ms-transition: background-color .5s linear; transition: background-color .5s linear; } body.scrolling .fl-page-header-wrap{background: '. $clr .' !important;-webkit-transition: background-color .5s linear; -moz-transition: background-color .5s linear; -o-transition: background-color .5s linear; -ms-transition: background-color .5s linear; transition: background-color .5s linear;}';

        if( 'yes' == $settings->hidden ) {
            $css .= 'header.fl-page-header {display: none;}';
        }

        $css .= '}';
    }elseif ('hidden' == $settings->header_style) {
		$css .= 'header.fl-page-header {display: none;}';
	}
    return $css;
}

// Add CSS Styles to this page's stylesheet
add_filter('fl_builder_render_js', 'tp_filter_render_js', 10, 3);
function tp_filter_render_js($js, $nodes, $global_settings)
{
    $height = 20;

    $settings = FLBuilderModel::get_layout_settings();
    if ('overlay' == $settings->header_style) {

        $js .= 'jQuery( document ).ready(function($) {
            $(window).scroll(function () {
                var scroll = $(window).scrollTop();';

        if( 'no' == $settings->hidden ) {
            $js .= 'if (scroll >= '. $settings->height .') {
                    $("body").addClass("scrolling");
                } else {
                    $("body").removeClass("scrolling");
                }';
        }else{
            $js .= 'if (scroll >= '. $settings->height .') {
                    $("header.fl-page-header").fadeIn("slow");
                    $("body").addClass("scrolling");
                } else {
                    $("header.fl-page-header").fadeOut("slow");
                    $("body").removeClass("scrolling");
                }';
        }

         $js .= '
            });
        });';
    }
    return $js;
}