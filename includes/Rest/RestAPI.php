<?php
/**
 * Rest.php
 */

namespace PLL_GTNB\Rest;
use \PLL_GTNB\Utils\JWTUtils;


if( !class_exists('\PLL_GTNB\Rest\RestAPI') ){
abstract class RestAPI extends \WP_REST_Controller
{

    /**
	 * Plugin's API namespace
	 * @var string
	 */
	const namespace = 'pll-gtnb';
	
	/**
	 * Plugin's API version
	 * @var string
	 */
	const version = '1';

	/**
	 * to be overriden by child classes
	 */
	const route = "";

	/**
	 * ApiController constructor.
	 */
	public function __construct() {
		
		add_filter('rest_api_init', [$this,'init_hooks']);		
	}
	
	public function init_hooks(){
		$this->register_api_routes();
		//add_filter('rest_pre_serve_request', [$this,'register_access_rules']);
	}

	/**
	 * registers rules for accessing the rest api
	 */
    public function register_access_rules( $headers ){
		$allowed_origins = [
			get_site_url(),         // Main site
		];
	
		if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
			$headers['Access-Control-Allow-Origin'] = $_SERVER['HTTP_ORIGIN'];
		} else {
			header("HTTP/1.1 403 Forbidden");
			exit;
		}
	
		return $headers;	
	}


	/**
	 * necessary to override from WP_REST_Controller
	 */
	public function register_routes()
	{
		$this->register_api_routes();
	}

    abstract function register_api_routes();

    /**
	 * @param null $name
	 *
	 * @return string
	 */
	protected function get_namespace(){
		return self::namespace.'/v'.self::version;
	}

    /**
	 * @param null $name
	 *
	 * @return string
	 */
	protected function get_route($name = null){
		$route = $name?:$this::route;
		return '/' . $route;
	}

	/**
	 * @param [] $data
	 * @param int $http_code
	 *
	 * @return \WP_REST_Response
	 */
	protected function response($data, $http_code = 200){
		return new \WP_REST_Response($data, $http_code);
	}


    /**
     * @param int $user_id
     * 
     * @return string userdata
     */
	public static function get_jwt_secret( $user_id=0 ){

        //get WP_USER pass
		$userdata = get_userdata($user_id);
		if( empty($userdata) )
			return "";

        //append secret string
		return $userdata->user_pass."k6!Lf5"; //secret must contain a number, a small and a capital letter and a symbol
	}

	/**
	 * get nonce value
     * 
     * @param int $user_id
     * 
     * @return string jwt
	 */
	public static function get_jwt( $user_id=0 ){

        //form token based on wp_user data
		$auth_token = Token::create(
			$user_id, //user id
			self::get_jwt_secret( $user_id ),
			time()+3600,           //expiration time 
			$_SERVER['SERVER_NAME'] //domain
		);
		return $auth_token;
	}

	/**
	 * checks if current user have permission to access and edit active trainings
	 * 
     * @param $request  WP_REST_REQUEST
     * 
     * @return bool
	 */
	public function check_jwt_permissions( $request ) {
		$token = "";
	
		// Get token from headers
		if (method_exists($request, 'get_header')) {
			$token = $request->get_header('X-Authentication-Token');
		}
	
		// Get token from request parameter if not found in headers
		if (empty($token) && method_exists($request, 'get_param')) {
			$token = $request->get_param('jwt') ?? "";
		}
	
		if (empty($token)) {
			return false;
		}
	
		// Extract token payload
		$token_payload = JWTUtils::get_token_payload($token);
	
		// Validate payload existence
		if (!$token_payload || !isset($token_payload['user_id'])) {
			return false;
		}
	
		// Ensure user_id is a valid number
		$token_user_id = intval($token_payload['user_id']);
		if ($token_user_id <= 0) {
			return false;
		}
	
		// Get the secret key for the token
		$secret = JWTUtils::get_jwt_secret($token_user_id);
		if (!$secret) {
			return false;
		}
	
		// Validate token
		if (!JWTUtils::validate_token($token, $secret)) {
			return false;
		}
	
		// Check if user has required permissions (e.g., editing posts)
		if (!user_can($token_user_id, 'edit_posts')) {
			return false;
		}
	
		return true;
	}

}
}