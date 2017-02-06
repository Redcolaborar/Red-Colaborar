<?php
if (!class_exists('Mediapresscustomizeradmin')) {
    class Mediapresscustomizeradmin {
        public static $instance;
        public function __construct() {
            add_action('init', array($this, 'init_hooks'));
            add_action( 'admin_init', array( $this, 'admin_init_hooks' ) );
        }
        function init_hooks() {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
        function admin_init_hooks() {
            
        }
        public static function create_instance() {
            if (is_null(self::$instance))
                self::$instance = new Mediapresscustomizeradmin();
            return self::$instance;
        }
        function admin_enqueue_scripts($hook)
        {
        }

    }

}
Mediapresscustomizeradmin::create_instance();


class Mediapresscustomizersettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'mediapress_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function mediapress_add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Mediapress Customizer  Settings', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'mediapress_customizer_options' );
        ?>
        <div class="wrap">
            <h1>Mediapress Customizer  Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'mediapress_customizer_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        add_settings_field(
            'enable_embed_url', // ID
            'Enable Embed Video Url Feature', // Title 
            array( $this, 'enable_embed_video_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );      
          
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['enable_embed_url'] ) )
            $new_input['enable_embed_url'] = sanitize_text_field( $input['enable_embed_url'] );
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function enable_embed_video_callback()
    {        
            $no_selcted = $yes_selcted = '';
            $enable_embed_url = $this->options['enable_embed_url'] ;      
                        
            if(empty($enable_embed_url) || $enable_embed_url == 'yes')
            {
                $yes_selcted = 'checked';
            }
            else {
                $no_selcted = 'checked' ;
            }                        
            echo '<input type="radio" id="enable_embed_url1" '.$yes_selcted.' name="mediapress_customizer_options[enable_embed_url]" value="yes"><label for="enable_embed_url1">Yes</label>&nbsp;&nbsp;
            <input type="radio" id="enable_embed_url2" '.$no_selcted.' name="mediapress_customizer_options[enable_embed_url]" value="no"><label for="enable_embed_url2">No</label>'
            . '<p id="home-description" class="description">If enable this feature users can add embed video URL with the posts in activity stream.</p>';
            
    }

}

$my_settings_page = new Mediapresscustomizersettings();
