<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'secrets.php';

function get_user_photos($fb) {
    try {
        $photos_request = $fb->get('/me/photos');
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage() . PHP_EOL;
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage() . PHP_EOL;
        exit;
    }
    $photos_edge = $photos_request->getGraphEdge();
    do {
        foreach ($photos_edge as $photos_node) {
            echo "Retrieving $photos_node[id]" . PHP_EOL;
            retrieve_photo($fb, $photos_node['id']);
        }
    } while ($photos_edge = $fb->next($photos_edge));
}

function retrieve_photo($fb, $id) {
    $request = $fb->get("/$id?fields=images");
    $decoded_request = $request->getDecodedBody();
    $photo_images = $decoded_request['images'];
    usort($photo_images, function($a, $b) {
        return $b['height'] <=> $a['height'];
    });
    $highest_resolution_image = $photo_images[0];
    download_file($highest_resolution_image['source'], $id);
}

function download_file($url, $id, $base_path = '/backup/') {
    $file_path = $base_path . $id . '.jpg';
    echo "Downloading $url to $file_path" . PHP_EOL;
    $file_fd = fopen($url, 'r');
    file_put_contents($file_path, $file_fd);
    fclose($file_fd);
}

$fb = new Facebook\Facebook([
    'app_id' => $app_id,
    'app_secret' => $app_secret,
    'default_graph_version' => 'v3.1',
    'default_access_token' => $access_token
    ]);

get_user_photos($fb);
?>