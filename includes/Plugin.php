<?php
/**
 * main plugin file
 */

namespace PLL_GTNB;

if( !class_exists('\PLL_GTNB\Plugin') ){

class Plugin{

    public function __construct(){
        $this->register_rest_api();
        add_action('init', [$this, 'register_blocks']);

    }
    
    /**
     * initializes Custom Gutenburg Blocks
     */
    public function register_blocks(){
        return [
            new Blocks\LanguageContent
        ];
    }

    /**
     * Registers REST endpoints
     */
    public function register_rest_api(){
        return [
            new \PLL_GTNB\Rest\TranslationsAPI
        ];
    }
}

}