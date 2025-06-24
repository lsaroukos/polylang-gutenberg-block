<?php 
/**
 * Main block definition class
 */

namespace PLL_GTNB\Blocks;

use \PLL_GTNB\Utils\StringUtils;

if( !\class_exists('\PLL_GTNB\Blocks\Block') ){

abstract class Block{

    /**
     * The block namespace.
     *
     * @var string
     */
    private $namespace = 'pll_gtnb';


    /**
     * The relative path to the directory where the block's files are stored.
     *
     * @var string
     */
    protected $blocks_root = ROOT_DIR . DIRECTORY_SEPARATOR . 'blocks';


    public function __construct(){

       $this->register_block();  

       //enqueue block specific scripts
       \add_action('wp_enqueue_scripts', [$this,'enqueue_scripts']);
    }

    /**
     * @return string path to current block src directory
     */
    protected function get_block_src_dir(){

        //get blocks directory name that corresponds to current (child) class name
        //e.g. MyBlock -> my-block
        $block_name = StringUtils::convertToKebabCase( StringUtils::get_base_class_name(static::class) );
        //return full path
        return implode( DIRECTORY_SEPARATOR, [$this->blocks_root,'dist',$block_name] );
    }

    /**
     * call wp register block function 
     */
    function register_block() {

        \register_block_type(
            $this->get_block_src_dir(), //get block.json path
            [
                'render_callback'   => [$this, 'render_callback']
            ]
        );
    }

    /**
     * returns html code to render. 
     * If not defined it returns null, meanind it should try rendering from js
     */
    function render_callback($attributes, $content, $block){
        //get html output
        ob_start();
        $this->render_html( $attributes, $content, $block );
        $html = ob_get_clean();
        
        return empty($html) ? null : $html;
    }

    /**
     * renders the blocks frontend code
     * 
     * @param $attributes
     * @param $content
     * @param $block
     * @return mixed
     */
    abstract function render_html( $attributes, $content, $block );

    /**
     * 
     */
    public function enqueue_scripts(){}
}
}