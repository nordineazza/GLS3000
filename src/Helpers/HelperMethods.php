<?php

namespace Helpers;

class HelperMethods {

    /**
     * Converti un array en JSON
     * @param $array Array
     * @param $nomColonne
     * @return string JSON
     */
    static function convertArrayToJson($array, $nomColonne){
        foreach ($array as $key => $value)
            $arrayToConvert[] = $value[$nomColonne];
        $json = json_encode($arrayToConvert);
        return $json;
    }
    /**
     * Retire les éléments vide d'un array.
     * @param $array d'éléments à traiter.
     * @return un array sans les éléments vides.
     */
    static function removeEmptyValueFromArray($array){
        foreach ($array as $key => $value) {
            if (empty($value) or $value == -1) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
?>