<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

class Helper
{
	public static function set_userID()
    {
        global $user_ID;

        $exists_cookie = false;
        $user_cookie = hexdec(uniqid()); // unique key
        $cookie_name = $_SERVER['SERVER_NAME'] . '_recomUser';

        if( $user_ID && get_option('recom_cookie_check') ) // user is logged in
        {
            $recom_cookie = get_user_meta( $user_ID, 'recom_cookie', true ); // get recom_cookie field from db

            if( empty($recom_cookie) ) // if not saved cookie for loggedin user
            {
                update_user_meta( $user_ID, 'recom_cookie', $user_cookie );
                $exists_cookie = true;
            }

            if( $recom_cookie != $_COOKIE[$cookie_name] ) // if cookie not equal to recom_cookie field
            {
                $user_cookie = $recom_cookie;
                $exists_cookie = true;
            }
        }

        if( !isset($_COOKIE[$cookie_name]) || $exists_cookie )
        {
            setcookie( $cookie_name, $user_cookie, strtotime('+33 years'), COOKIEPATH, COOKIE_DOMAIN, false );
            $_COOKIE[$cookie_name] = $user_cookie;
        }
        else
        {
            $user_cookie = $_COOKIE[$cookie_name];
        }

        $user_ID = $user_cookie;

        return $user_ID;
    }

    public static function get_userID()
    {
    	$cookie_name = $_SERVER['SERVER_NAME'] . '_recomUser';
    	// You can set them with dots using Javascript, but when you go to access them, the dots magically become underscores [ _ ]
        $cookie_name = str_replace('.', '_', $cookie_name);

        $user_ID = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : self::set_userID();

        return $user_ID;
    }

    public static function get_userID_hash()
    {
        return '#.rec.' . self::get_userID();
    }

    public static function add_http( $url )
    {
        if ( !preg_match( "~^(?:f|ht)tps?://~i", $url ) )
            $url = 'http://' . $url;

        return $url;
    }

    public static function add_hash_to_links( $text, $hash = '' ) {
        $text = preg_replace('/<a(.*)href="(.*?)(?:\#\.rec\.\d+)?"(.*)>/', '<a$1href="$2'.$hash.'"$3>', $text);

        return $text;
    }

    public static function get_ids( $items ) {
        $post_ids = [];

        if( $items )
        {
            foreach( $items as $item )
            {
                if( strpos( $item[0], 'wp-' ) !== false )
                    $post_ids[] = str_replace( 'wp-', '', $item[0] );
            }
        }

        return $post_ids;
    }
}