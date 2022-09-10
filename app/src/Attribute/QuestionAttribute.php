<?php

namespace App\Attribute;

use App\Entity\Question;
use App\Entity\QuestionAverage;
use App\Entity\QuestionBoolean;
use App\Entity\QuestionOption;
use App\Interface\AttributeInterface;
use App\Interface\QuestionInterface;


class QuestionAttribute implements AttributeInterface
{
    const MAP = [
        'question_average' => QuestionAverage::class,
        'question_boolean' => QuestionBoolean::class,
        'question_option' => QuestionOption::class
    ];

    public static function getTypes(): array
    {
        return array_map(fn($e) => ucfirst($e), array_keys(self::MAP));
    }

    private static function LabelNames(): array
    {
        $array = self::MAP;
        array_walk($array, fn(&$a, $b) => $a = ucfirst($b));
        return $array;
    }

    public static function getTypeName($typeShortName): string
    {
        return self::LabelNames()[$typeShortName] ?? "Unknown type ($typeShortName)";
    }

    /**
     * @throws \Exception
     */
    public static function getEntity(string $type): QuestionInterface
    {
        return match(strtolower($type)) {
            'question_average' => new QuestionAverage(),
            'question_boolean' =>  new QuestionBoolean(),
            'question_option' =>  new QuestionOption(),
            default => throw new \Exception('Not Type Question Defined')
        };
    }

    /**
     * @throws \Exception
     */
    public static function getRepository(?string $type,$entityManager)
    {
        return array_key_exists(strtolower($type),self::MAP) ? $entityManager->getRepository(self::MAP[strtolower($type)])
            :  $entityManager->getRepository(Question::class);
    }

    public static function getKeyParam(string $class): int|string|null
    {
        return array_flip(self::MAP)[$class] ?? null;
    }
}