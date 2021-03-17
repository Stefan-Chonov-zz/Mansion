<?php


namespace Mansion;

use Mansion\Cache\CacheManager;
use Mansion\External\Api as ExternalApi;
use Mansion\Utils\HttpRequestMethod;
use Mansion\Utils\Request;
use Mansion\Utils\Utils;

class Api extends Request
{
    public const SUPPORTED_POSTS_LOCATIONS = __DIR__ . "/../assets/locations.json";

    /**
     * @var CacheManager|null
     */
    private ?CacheManager $cacheManager;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->cacheManager = new CacheManager();
    }

    /**
     * Handle HTTP Request.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        $response = array(
            'status' => 200,
            'message' => 'Success',
            'data' => array(),
        );

        switch ($_SERVER["REQUEST_METHOD"]) {
            case HttpRequestMethod::GET:
                $response['data'] = $this->handleGetRequest($_GET);
                break;
            case HttpRequestMethod::POST:
            case HttpRequestMethod::PUT:
            case HttpRequestMethod::DELETE:
                $response['status'] = 400;
                $response['message'] = 'Unsupported Request Method';
                break;
            default:
                $response['status'] = 404;
                $response['message'] = 'Invalid Request Method';
                break;
        }

        parent::response($response);
    }

    /**
     * Handling GET Requests.
     *
     * @param array $params
     * @return array
     */
    private function handleGetRequest(array $params): array
    {
        $result = array();

        $posts = $this->loadPosts();

        switch (strtolower($_SERVER['PATH_INFO'])) {
            case '/nearest-posts/uk':
                $this->queryParametersValidation();

                $strippedPostcode = str_replace(' ', '', $params['postcode']);
                $post = $this->cacheManager->getByKey(strtoupper($strippedPostcode));
                if (!isset($post)) {
                    return $result;
                }

                if (isset($params['radius'])) {
                    $result = $this->findNearestPostsInRadius($post, $posts, $params['radius']);
                } else {
                    $result = $post;
                }
                break;
            case '/posts/uk':
                $result = $posts;
                break;
        }

        return $result;
    }

    /**
     * Checks is query parameters are valid.
     *
     * @return void
     */
    private function queryParametersValidation(): void
    {
        $response = array(
            'status' => 400,
            'message' => '',
            'data' => array(),
        );

        // Validate 'postcode' parameter.
        if (!isset($_GET['postcode'])) {
            $response['message'] = "Parameter 'postcode' is required";
            parent::response($response);
        } else {
            $isUKPostcodeValid = Utils::isUKPostcodeValid(strtoupper($_GET['postcode']));
            if (!$isUKPostcodeValid) {
                $response['message'] = sprintf("Invalid UK postcode '%s'", $_GET['postcode']);
                parent::response($response);
            }
        }

        // Validate 'radius' parameter.
        if (isset($_GET['radius']) && !is_numeric($_GET['radius'])) {
            $response['message'] = sprintf("Invalid '%s' value", $_GET['postcode']);
            parent::response($response);
        }
    }

    /**
     * Retrieves Posts from Cache or JSON file.
     *
     * @return array Returns posts.
     */
    private function loadPosts(): array
    {
        // Load Posts from Cache.
        $posts = $this->cacheManager->getAll();
        try {
            // Load supported Posts from file.
            $supportedPosts = Utils::parseJsonFile(self::SUPPORTED_POSTS_LOCATIONS);

            // Checks for differences between cached Posts and supported Posts.
            $diffs = array();
            $isCachedDataSynchronized = Utils::isCachedDataSynchronized($posts, $supportedPosts, $diffs);

            // Load Posts in cache.
            if (!$isCachedDataSynchronized) {
                $supportedPostCodes = array();
                foreach ($supportedPosts as $post) {
                    $supportedPostCodes[] = $post['postcode'];
                }

                $rawExternalApiPosts = ExternalApi::getUKPostsByPostcodes($supportedPostCodes);
                $externalApiPosts = json_decode($rawExternalApiPosts, true);
                $apiPosts = array();
                if (isset($externalApiPosts['status']) && $externalApiPosts['status'] === 200) {
                    foreach ($externalApiPosts['result'] as $item) {
                        $apiPosts[] = $item['result'];
                    }
                }

                foreach ($diffs as $key => $diff) {
                    foreach ($apiPosts as $post) {
                        if (isset($post)) {
                            $strippedPostcode = str_replace(' ', '', $post['postcode']);
                            if ($strippedPostcode === $key) {
                                $posts[$strippedPostcode]['name'] = $diff['name'];
                                $posts[$strippedPostcode]['postcode'] = $post['postcode'];
                                $posts[$strippedPostcode]['longitude'] = $post['longitude'];
                                $posts[$strippedPostcode]['latitude'] = $post['latitude'];
                                $posts[$strippedPostcode]['distance'] = 0;

                                break;
                            }
                        }
                    }
                }

                $this->cacheManager->save($posts);
            }
        } catch (\Exception $ex) {
            // TODO: Handle Exception
        }

        return $posts;
    }

    /**
     * Retrieves nearest Posts in radius.
     *
     * @param array $startPost Starting point.
     * @param array $endPosts List of end points.
     * @param float $radius Radius of searching.
     * @return array
     */
    private function findNearestPostsInRadius(array $startPost, array $endPosts, float $radius): array
    {
        try {
            $nearestPosts = array();
            $endPosts = Utils::calculateDistanceBetweenPosts($startPost, $endPosts);
            foreach ($endPosts as $startPost) {
                if ($startPost['distance'] <= $radius) {
                    $nearestPosts[] = $startPost;
                }
            }

            return $nearestPosts;
        } catch (\Exception $ex) {
            // TODO: Handle Exception
        }

        return array();
    }
}