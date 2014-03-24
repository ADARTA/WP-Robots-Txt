<?php
/**
 * WP Robots Txt Version2
 *
 * Copyright 2014 by the contributors
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @package     WPRobotsTxtV2
 * @copyright   WP-Robots-Txt-Version2 - Copyright 2014 by the contributors
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

/**
 * Wrapper for all our admin area functionality.
 * 
 * @since 0.1
 */
class CD_RDTE_Admin_Page
{
    private static $ins = null;

    protected $setting = 'cd_rdte_content';

    public static function instance()
    {
        if (null === self::$ins) {
            self::$ins = new self();
        }

        return self::$ins;
    }

    /**
     * Kick everything off.
     * 
     * @since   1.0
     * @access  public
     * @uses    add_action
     * @return  void
     */
    public static function init()
    {
        add_action('admin_init', array(self::instance(), 'settings'));
    }
    
    /**
     * Registers our setting and takes care of adding the settings field
     * we need to edit our robots.txt file
     * 
     * @since   1.0
     * @access  public
     * @uses    register_setting
     * @uses    add_settings_field
     * @return  void
     */
    public function settings()
    {
        register_setting(
            'reading', 
            $this->setting,
            array($this, 'cleanSetting')
        );

        add_settings_section(
            'robots-txt',
            __('Robots.txt Settings', 'wp-robots-txt-v2'),
            '__return_false',
            'reading'
        );

        add_settings_field(
            'cd_rdte_robots_content',
            __('Robots.txt Content', 'wp-robots-txt-v2'),
            array($this, 'field'),
            'reading',
            'robots-txt',
            array('label_for' => $this->setting)
        );
    }

    /**
     * Callback for the settings field.
     * 
     * @since   1.0
     * @access  public
     * @uses    get_option
     * @uses    esc_attr
     * @return  void
     */
    public function field()
    {
        $public = get_option('blog_public');

        $already_set = get_option($this->setting);
        if ($already_set) {
           $content = get_option($this->setting);
        } else {
           $content = $this->getDefaultRobots();          
        }

        printf(
            '<textarea name="%1$s" id="%1$s" rows="10" class="large-text">%2$s</textarea>',
            esc_attr($this->setting),
            esc_textarea($content)
        );

        echo '<p class="description">';
        if ($already_set) {
            if ($public) {
                _e('The content of your robots.txt file.  Clear contents above and save to restore the default.','wp-robots-txt-v2');
            } else {
                _e('Not using the settings above. Using default as shown below. Uncheck the Discourage checkbox above to use the settings above.<br/> Make sure you do not have a physical robots.txt in your web root.','wp-robots-txt-v2');
            }
        } else {
            echo '<label style="color:#f00;font-weight:bold">';
             if ($public) {
                _e('You must Save Changes to make overrides above active.<br/>It is OK to not save, but the Wordpress default below will be your active robots.txt','wp-robots-txt-v2');
            } else {
                _e('Not using the settings above. Using default as shown below. Uncheck the Discourage checkbox above to use the settings above.<br/> Make sure you do not have a physical robots.txt in your web root.','wp-robots-txt-v2');
            }
           echo '</label>';             
        }
        echo '<div><iframe src="/robots.txt" height="120px" width="100%"></iframe></div>';
        echo '</p>';
    }

    /**
     * Strips tags and escapes any html entities that goes into the 
     * robots.txt field
     * 
     * @since 1.0
     * @uses esc_html
     * @uses add_settings_error
     */
    public function cleanSetting($in)
    {
        if(empty($in)) {
            // TODO: why does this kill the default settings message?
            add_settings_error(
                $this->setting,
                'cd-rdte-restored',
                __('Robots.txt restored to default.', 'wp-robots-txt-v2'),
                'updated'
            );
        }

        return esc_html(strip_tags($in));
    }
    
    /**
     * Get the default robots.txt content.  This is copied straight from
     * WP's `do_robots` function
     * 
     * @since   1.0
     * @access  protected
     * @uses    get_option
     * @return  string The default robots.txt content
     */
    protected function getDefaultRobots()
    {
        $public = get_option('blog_public');

        $output = "# WordPress default, research best settings.\n";
        $output .= "User-agent: *\n";
        if (!$public) {
            $output .= "Disallow: /\n";
        } else {
            $path = parse_url(site_url(), PHP_URL_PATH);
            $output .= "Disallow: $path/wp-admin/\n";
            $output .= "Disallow: $path/wp-includes/\n";

   /**
     * Much debate on what should be default, so we will leave it out for now!
     * The Allow is also not used by all bots, so we leave it out for now also.
     *
     *       $contentpath = parse_url(content_url(), PHP_URL_PATH); // parse_url(WP_CONTENT_URL, PHP_URL_PATH);
     *       if ($contentpath !== $path .'/wp-content/')
     *           $output .= "Disallow: $path/wp-content/plugins\n";
     *       elseif ($contentpath !== '')
     *           $output .= "Disallow: $contentpath/plugins\n";
     *      if (get_option('upload_url_path')) {
     *           $mediapath = parse_url(get_option('upload_url_path'), PHP_URL_PATH);
     *           $output .= "Allow: $mediapath/\n";
     *       }
     */
            
        }

        return $output;
    }
}
