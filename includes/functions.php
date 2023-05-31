<?php
// functions.php

use Orhanerday\OpenAi\OpenAi;

// Function to generate content for a WordPress post using ChatGPT
function aioai_generate_post_content($title)
{
    // Set up OpenAI API credentials
    $open_ai_key = get_option('API_KEY');
    $open_ai = new OpenAI($open_ai_key);

    $chat = $open_ai->chat([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a WordPress user requesting a blog post about a specific topic.',
            ],
            [
                'role' => 'user',
                'content' => 'Generate a new blog post about ' . $title,
            ],
        ],
    ]);

    // Decode response
    $response = json_decode($chat, true);

    // Return the generated content
    return $response['choices'][0]['message']['content'];
}

// Hook into the 'wp_insert_post_data' action to automatically generate content for a new post
function aioai_auto_generate_post_content($data, $postarr)
{
    if (isset($_POST['generate_content'])) {
        $title = !empty($postarr['post_title']) ? $postarr['post_title'] : 'topic';
        $data['post_content'] = aioai_generate_post_content($title);
    }
    return $data;
}
add_filter('wp_insert_post_data', 'aioai_auto_generate_post_content', 10, 2);

// Generate meta description content using OpenAI
function aioai_generate_meta_description_content($title, $content)
{
    // Set up OpenAI API credentials
    $open_ai_key = get_option('API_KEY');
    $open_ai = new OpenAi($open_ai_key);

    $chat = $open_ai->chat([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a WordPress user generating a meta description.',
            ],
            [
                'role' => 'user',
                'content' => 'Generate a meta description for ' . $title . '.',
            ],
        ],
    ]);

    // Decode response
    $response = json_decode($chat, true);

    // Return the generated content
    return $response['choices'][0]['message']['content'];
}

// Handle generation and update of meta description
function aioai_generate_meta_description($post_id)
{
    if (isset($_POST['generate_meta_description'])) {
        $title = get_the_title($post_id);
        $content = get_post_field('post_content', $post_id);

        // Generate meta description content using OpenAI
        $meta_description = aioai_generate_meta_description_content($title, $content);

        // Update meta description
        update_post_meta($post_id, 'meta_description', $meta_description);

        // Update meta tag for the page
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_description);
    }
}
add_action('save_post', 'aioai_generate_meta_description');