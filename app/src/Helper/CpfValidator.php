<?php

namespace App\Helper;

class CpfValidator {
    private static ?string $cpf;

    public static function validate($cpf)
    {
        self::$cpf = $cpf;
        if (self::formatValidator()) {
            if (self::calcValidator()) {
                return true;
            }
        }
        return false;
    }

    private static function formatValidator(): bool
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', self::$cpf) > 0;
    }

    private static function calcValidator(): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', self::$cpf);
        $digitoA = 0;
        $digitoB = 0;
        for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
            $digitoA += $cpf[$i] * $x;
        }
        for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
            if (str_repeat($i, 11) == $cpf) {
                return false;
            }
            $digitoB += $cpf[$i] * $x;
        }

        $somaA = (($digitoA % 11) < 2) ? 0 : 11 - ($digitoA % 11);
        $somaB = (($digitoB % 11) < 2) ? 0 : 11 - ($digitoB % 11);

        if ($somaA != $cpf[9] && $somaB != $cpf[10]) {
            return false;
        } else {
            return true;
        }
    }
}