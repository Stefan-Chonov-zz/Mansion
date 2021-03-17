<?php


namespace Mansion\External;

class Api
{
    protected const API_BASE_URL = 'https://api.postcodes.io';
    protected const API_POSTCODES_ENDPOINT = '/postcodes/';
    protected const API_POSTCODE_VALIDATION_ENDPOINT = '/postcodes/%s/validate';

    /**
     * Retrieves posts by given list of postcodes.
     *
     * @param array $postcodes List with postcodes.
     * @return string Returns JSON string.
     */
    public static function getUKPostsByPostcodes(array $postcodes): string
    {
        $ch = curl_init();

        $requestUrl = self::API_BASE_URL . self::API_POSTCODES_ENDPOINT;
        $payload = json_encode(array("postcodes" => $postcodes));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            // TODO: Handle Curl Request Errors - curl_error($ch)
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Checks is given postcode valid.
     *
     * @param string $postcode UK Postcode.
     * @return string Returns JSON string.
     */
    public static function checksUKPostcodeValidity(string $postcode): string
    {
        $ch = curl_init();

        $requestUrl = sprintf(self::API_BASE_URL . self::API_POSTCODE_VALIDATION_ENDPOINT, $postcode);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            // TODO: Handle Curl Request Errors - curl_error($ch)
        }

        curl_close($ch);

        return $response;
    }
}