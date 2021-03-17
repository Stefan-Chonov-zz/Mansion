<?php


namespace Mansion\Web\Models;

class Post implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     */
    private string $postcode = '';

    /**
     * @var float
     */
    private float $longitude = 0.0;

    /**
     * @var float
     */
    private float $latitude  = 0.0;

    /**
     * @var float
     */
    private float $distance  = 0.0;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->name = '';
        $this->postcode = '';
        $this->longitude = 0.0;
        $this->latitude = 0.0;
        $this->distance = 0.0;
    }

    /**
     * Method creates an instance of the current model class.
     *
     * @param string $json Specifies input JSON string to create the object from.
     *
     * @return Post Returns the resulting model object.
     * @throws \InvalidArgumentException Throws exception on invalid/missing JSON data.
     */
    public static function from_json(string $json): self
    {
        try {
            $data = json_decode(
                $json,
                true,
                512,
                JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException(
                sprintf("Invalid JSON input data - %s", $e->getMessage()),
                $e->getCode()
            );
        }

        $class = get_called_class();

        return new $class($data);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'postcode' => $this->postcode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => $this->distance
        ];
    }
}