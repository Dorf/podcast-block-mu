<?php
/**
 * Plugin Name: BLAC Podcast Block
 * Description: A custom block to display Buzzsprout podcast episodes.
 * Version: 1.1
 * Author: Happy Hippopotam.us
 */

// Ensure the feed library is loaded early for proper SimplePie functionality.
include_once( ABSPATH . WPINC . '/feed.php' );

/**
 * Register the custom podcast block.
 */
function register_custom_podcast_block() {
    // This reads block.json from the blac-podcast folder.
    register_block_type( __DIR__ . '/blac-podcast/block.json' );
}
add_action( 'init', 'register_custom_podcast_block' );

/**
 * Render callback function for the custom podcast block.
 */
function render_custom_podcast_block( $attributes ) {
    return '<p>Test output from BLAC Buzzsprout Podcasts block.</p>';
    // Define the Buzzsprout RSS feed URL.
    $feed_url = 'https://feeds.buzzsprout.com/2172386.rss';

    // For debugging, bypass the transient cache and fetch the feed every time.
    $rss = fetch_feed( $feed_url );
    if ( is_wp_error( $rss ) ) {
        return '<p>Unable to fetch podcast episodes at this time.</p>';
    }
    $maxitems   = $rss->get_item_quantity( 10 );
    $feed_items = $rss->get_items( 0, $maxitems );

    // Build a simple array with only the necessary data.
    $items_data = array();
    foreach ( $feed_items as $item ) {
        $items_data[] = array(
            'title'       => $item->get_title(),
            'permalink'   => $item->get_permalink(),
            'description' => $item->get_description(),
            'audio'       => ( $item->get_enclosures() && ! empty( $item->get_enclosures() ) )
                                ? $item->get_enclosures()[0]->get_link()
                                : '',
        );
    }

    if ( empty( $items_data ) ) {
        return '<p>No episodes found.</p>';
    }

    // For debugging, log the data to your error log.
    error_log( print_r( $items_data, true ) );

    // Begin output – customize as needed.
    $output = '<div class="custom-podcast">';
    foreach ( $items_data as $item ) {
        $title       = esc_html( $item['title'] );
        $link        = esc_url( $item['permalink'] );
        // Process the description through the_content filter for formatting.
        $description = apply_filters( 'the_content', $item['description'] );
        $audio_url   = esc_url( $item['audio'] );

        $output .= '<div class="podcast-episode">';
        $output .= '<h2 class="episode-title"><a href="' . $link . '">' . $title . '</a></h2>';

        if ( $audio_url ) {
            $output .= '<audio class="episode-player" controls src="' . $audio_url . '">';
            $output .= 'Your browser does not support the audio element.';
            $output .= '</audio>';
        }

        $output .= '<div class="episode-description">' . $description . '</div>';
        $output .= '<a class="episode-link" href="' . $link . '">View on Buzzsprout</a>';
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}