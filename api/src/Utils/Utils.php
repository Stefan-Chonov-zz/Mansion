<?php


namespace Mansion\Utils;

use Mansion\External\Api;

class Utils
{
    /**
     * Retrieves JSON object.
     *
     * @param string $file
     * @return array
     */
    public static function parseJsonFile(string $file): array
    {
        try {
            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true);
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        return array();
    }

    /**
     * Retrieves array of objects.
     *
     * @param array $data
     * @param string $class
     * @return array
     */
    public static function jsonToArrayOfObjects(array $data, string $class): array
    {
        $objects = array();

        try {
            foreach ($data as $item) {
                /** @var $class $post */
                $object = self::jsonDecodeObject(json_encode($item), $class);
                $objects[] = $object;
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        return $objects;
    }

    /**
     * Decoding JSON string to an a object.
     *
     * @param string $json
     * @param string $class
     * @return object
     * @throws \ReflectionException
     */
    public static function jsonDecodeObject(string $json, string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $instance = $reflection->newInstanceWithoutConstructor();
        $json = json_decode($json, true);
        $properties = $reflection->getProperties();
        foreach ($properties as $key => $property) {
            $property->setAccessible(true);
            if (isset($json[$property->getName()])) {
                $property->setValue($instance, $json[$property->getName()]);
            }
        }
        return $instance;
    }

    /**
     * Calculates distance between posts.
     *
     * @param array $post Starting point.
     * @param array $posts List of end points.
     * @return array Returns list with distances.
     */
    public static function calculateDistanceBetweenPosts(array $post, array $posts): array
    {
        $results = array();

        foreach ($posts as $endPostcode => $endPost) {
            if (isset($endPost)) {
                $results[$endPostcode] = $endPost;
                $results[$endPostcode]['distance'] = self::calculateDistanceBetweenTwoPoints(
                    $post['latitude'],
                    $post['longitude'],
                    $endPost['latitude'],
                    $endPost['longitude']
                );
            }
        }

        return $results;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     *
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function calculateDistanceBetweenTwoPoints(
        float $latitudeFrom,
        float $longitudeFrom,
        float $latitudeTo,
        float $longitudeTo,
        int $earthRadius = 6371) : float
    {
        // Convert from degrees to radians.
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Checks UK Postcode validity.
     *
     * @param string $postcode
     * @return bool
     */
    public static function isUKPostcodeValid(string $postcode): bool
    {
        $isUKPostcodeValid = false;

        $isUKPostcodePatternValid = self::validateUKPostcodePattern($postcode);
        if (!$isUKPostcodePatternValid) {
            return $isUKPostcodeValid;
        }

        $rawUKPostcodeValidity = Api::checksUKPostcodeValidity(strtoupper($postcode));
        $ukPostcodeValidity = json_decode($rawUKPostcodeValidity, true);
        if (isset($ukPostcodeValidity['status']) && $ukPostcodeValidity['status'] === 200) {
            $isUKPostcodeValid = $ukPostcodeValidity['result'];
        }

        return $isUKPostcodeValid;
    }

    /**
     * Checks UK Postcode Pattern tailored by UK Government Data Standard.
     *
     * @param string $postcode UK Postcode.
     * @return bool
     */
    public static function validateUKPostcodePattern(string $postcode): bool
    {
        return (bool)preg_match(
            "~^(GIR 0AA)|(TDCU 1ZZ)|(ASCN 1ZZ)|(BIQQ 1ZZ)|(BBND 1ZZ)"
            . "|(FIQQ 1ZZ)|(PCRN 1ZZ)|(STHL 1ZZ)|(SIQQ 1ZZ)|(TKCA 1ZZ)"
            . "|[A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]"
            . "|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))"
            . "|[0-9][A-HJKS-UW])\\s?[0-9][ABD-HJLNP-UW-Z]{2}$~i",
            $postcode);
    }

    /**
     * Checks is cached data are synchronized.
     *
     * @param array $cachedPosts
     * @param array $supportedPosts
     * @param array $diffs
     * @return bool
     */
    public static function isCachedDataSynchronized(array $cachedPosts, array $supportedPosts, array &$diffs = array()): bool
    {
        foreach ($supportedPosts as $supportedPost) {
            $strippedPostcode = str_replace(' ', '', $supportedPost['postcode']);
            if (!isset($cachedPosts[$strippedPostcode])) {
                $diffs[$strippedPostcode] = $supportedPost;
            }
        }

        return (bool)!(count($diffs) > 0);
    }
}