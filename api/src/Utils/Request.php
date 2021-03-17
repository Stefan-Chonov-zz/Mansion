<?php


namespace Mansion\Utils;


class Request
{
    /**
     * Echoing response.
     *
     * @param array $response
     * @return void
     */
    protected function response(array $response): void
    {
        switch ($response['status']) {
            case 200 :
                header('HTTP/1.1 200 OK');
                break;
            case 400 :
                header('HTTP/1.1 400 Bad Request');
                break;
            case 404 :
                header('HTTP/1.1 404 Not Found');
                break;
            case 500 :
                header('HTTP/1.1 500 Internal Server Error');
                break;
            default:
                // TODO
                break;
        }

        echo json_encode($response);
        die();
    }
}