<?php 
/**
 * Translations API
 */
namespace PLL_GTNB\Rest;


if( !class_exists('PLL_GTNB\Rest\TranslationsAPI') ){
class TranslationsAPI extends RestAPI
{

    /**
	 * Override default route
	 * @var string
	 */
	const route = 'translations';

    /**
     * API Endpoint, resolves on /wp-json/pll-gtnb/v1/
     * 
     * register controller routes
     */
    public function register_api_routes()
    {
        // resolves at /wp-json/pll-gtnb/v1/translations/languages
        register_rest_route( $this->get_namespace(), $this->get_route() . '/languages',[
            [
                'methods'   =>  \WP_Rest_Server::READABLE,
                'callback'  =>  [$this, 'get_active_languages'],
                'permission_callback'    => '__return_true',  
            ]
        ]);
    }

    /**
     * get a list of all registered translations
     */
    public function get_active_languages( $request ){

        // Get active Polylang languages
        if (function_exists('pll_the_languages')) {
            $languages = \pll_the_languages(['raw' => 1]);
        } else {
            $languages = [];
        }

        return $this->response([
            'status'    => 'success',
            'languages' =>  $languages
        ]);

        exit;
    }
}
}