<?php


namespace App\Helper;


class StringResources
{

    static function contains($string,$need): bool
    {
        return str_contains($string, $need);
    }

    static function generateRandom(): string
    {
        return str_replace('-','',date('y-m-d')).mt_rand(10000,99999);
    }

    static function turnIntoId(String $string): string
    {
        return self::addHyphen(self::toLower(self::removeAccents(self::cleanString($string))));
    }
    
    static function cleanString($string): string|null
    {

        $utf8 = [
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        ];
        $string = preg_replace(array_keys($utf8), array_values($utf8), $string);
        return self::removeDuplicateSpace(trim(preg_replace("/[^a-zA-Zà-úÀ-Ú0-9+\s]/", "", self::removeHyphen($string))));
    }
    
    static function removeHyphen($string): string|null
    {
        return str_replace('-',' ',$string);
    }
    
    static function addHyphen($string): string|null
    {
        return preg_replace("/[^a-zA-Z0-9-+]/", "-",$string);
    }
        
    static function toLower($string): string|null
    {
        return strtolower($string);
    }
    
    static function removeNewLines($string): string|null
    {
        return preg_replace('~[\r\n\/]+~', '', $string);
    }

    static function removeDuplicateSpace(string $string): string|null
    {
        return preg_replace('~[\s]+~', ' ', $string);
    }

    static private function removeAccents(String $string): string
    {
        $accents = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú','@');
        $noAccents = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U','a');
        return htmlentities(str_replace($accents, $noAccents,$string));    
    }

    static function onlyDigits(String $string): string
    {
        return preg_replace("/[^0-9]/", "",$string);
    }



}
