<?php


namespace Mansion\Web\Controllers;

use Mansion\Web\Core\Controller;
use Mansion\Web\Core\RequestMethod;
use Mansion\Web\External\Api;
use Mansion\Web\Utils\Utils;

class Home extends Controller
{
    /**
     * Main point of controller.
     *
     * @return void
     */
    public function index(): void
    {
        $response = array(
            'postcode' => '',
            'radius' => '',
            'dropDownRadius' => array(
                0 => 'Radius',
                1 => '1 KM',
                2 => '2 KM',
                3 => '3 KM',
                5 => '5 KM',
                10 => '10 KM',
                20 => '20 KM',
                50 => '50 KM',
                100 => '100 KM'
            )
        );

        if ($_SERVER['REQUEST_METHOD'] === RequestMethod::GET && count($_GET) > 0) {
            if (isset($_GET['postcode']) && Utils::validateUKPostCode($_GET['postcode'])) {
                $response['postcode'] = $_GET['postcode'];
            }

            if (isset($_GET['radius']) && is_numeric($_GET['radius']) &&
                array_key_exists($_GET['radius'], $response['dropDownRadius'])) {
                $response['radius'] = $_GET['radius'];
            }
        }

        parent::view('Home/index', $response);
    }

    /**
     * Get UK posts.
     *
     * @return void
     */
    public function getUKPosts(): void
    {
        $response = array(
            'status' => 200,
            'message' => 'Success',
            'data' => array(),
        );

        if ($_SERVER['REQUEST_METHOD'] === RequestMethod::GET) {
            $rawUKPosts = Api::getUKPosts();
            $ukPosts = json_decode($rawUKPosts, true);
            if (isset($ukPosts['status']) && $ukPosts['status'] === 200) {
                $response['data'] = $ukPosts['data'];
            }
        }

        echo json_encode($response);
    }

    /**
     * Find nearest UK posts.
     *
     * @return void
     */
    public function findNearestUKPosts(): void
    {
        $response = array(
            'status' => 200,
            'message' => 'Success',
            'data' => array(),
        );

        if ($_SERVER['REQUEST_METHOD'] === RequestMethod::GET) {
            if (isset($_GET['postcode']) && !empty($_GET['postcode'])) {
                if (Utils::validateUKPostCode($_GET['postcode'])) {
                    $radius = 0;
                    if (isset($_GET['radius']) && is_numeric($_GET['radius'])) {
                        $radius = $_GET['radius'];
                    }

                    $rawNearestPosts = Api::getNearestUKPosts($_GET['postcode'], $radius);
                    $nearestPosts = json_decode($rawNearestPosts, true);
                    if (isset($nearestPosts['status']) && $nearestPosts['status'] === 200) {
                        $response['data'] = $nearestPosts['data'];
                    }
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'Invalid UK Postcode';
                }
            } else {
                $rawPosts = Api::getUKPosts();
                $posts = json_decode($rawPosts, true);
                if (isset($posts['status']) && $posts['status'] === 200) {
                    $response['data'] = $posts['data'];
                }
            }
        }

        echo json_encode($response);
    }
}