<?php

namespace Mansion\Web\Utils;

class Utils
{
    /**
     * Checks UK Postcode Pattern tailored by UK Government Data Standard.
     *
     * @param string $postcode UK Postcode.
     * @return bool
     */
    public static function validateUKPostCode(string $postcode): bool
    {
        return (bool)preg_match(
            "~^(GIR 0AA)|(TDCU 1ZZ)|(ASCN 1ZZ)|(BIQQ 1ZZ)|(BBND 1ZZ)"
            . "|(FIQQ 1ZZ)|(PCRN 1ZZ)|(STHL 1ZZ)|(SIQQ 1ZZ)|(TKCA 1ZZ)"
            . "|[A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]"
            . "|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))"
            . "|[0-9][A-HJKS-UW])\\s?[0-9][ABD-HJLNP-UW-Z]{2}$~i",
            $postcode);
    }
}