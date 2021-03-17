<?php


namespace Mansion\Web\External;

class Api
{
    /**
     * Retrieves UK Posts
     *
     * @return bool|string
     */
    public static function getUKPosts(): bool|string
    {
        $ch = curl_init();

        $requestUrl = sprintf($_ENV['MANSION_API_HOST'] . $_ENV['MANSION_API_UK_POSTS_ENDPOINT']);

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PORT, $_ENV['MANSION_API_PORT']);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            // TODO: Handle Curl Request Errors - curl_error($ch)
        }

        curl_close($ch);

        return $response;
    }

    /**
     * @param string $postcode
     * @param float $radius
     * @return bool|string
     */
    public static function getNearestUKPosts(string $postcode, float $radius = 0): bool|string
    {
        $ch = curl_init();

        $params = http_build_query(
            array('postcode' => $postcode, 'radius' => $radius),
            null,
            null,
            PHP_QUERY_RFC3986
        );
        $requestUrl = sprintf($_ENV['MANSION_API_HOST'] . $_ENV['MANSION_API_NEAREST_UK_POSTS_ENDPOINT'] . "?%s", $params);

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PORT, $_ENV['MANSION_API_PORT']);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            // TODO: Handle Curl Request Errors - curl_error($ch)
        }

        curl_close($ch);

        return $response;
    }
}