<?php

namespace App\Helper;

use App\Attribute\QuestionAttribute;
use App\Entity\QuestionAverage;


class ReportHelper
{

    public static function groupRotation($report){
        $data = [];
        foreach ($report as $value){
            $data[$value['shift']][] = $value;
            unset($value['shift']);
        }
        return $data;
    }
    public static function groupWeekDay(array $report):array
    {
        $data = [];
        foreach ($report as $value){
            $data[$value['weekday']][] = $value;
            unset($value['weekday']);
        }
        return $data;
    }

    public static function groupQuestion(array $report):array
    {
        $data = [];
        foreach ($report as $value){
            if(!array_key_exists($value['question'], $data)) {
                $data[$value['question']]['type'] = $value['type'];
                $data[$value['question']]['enunciation'] = $value['enunciation'];
            }
            $data[$value['question']]['replies'][] = ['reply' => $value['reply'], 'repeated' => $value['repeated']];
            $data[$value['question']]['amount'] = self::getAmountReplies($data[$value['question']]['replies']);
        }
        return $data;
    }

    public static function calculateMetrics(array $groups):array
    {
        foreach ($groups as $key => $value){
            if($value['type']  == QuestionAttribute::getKeyParam(QuestionAverage::class)){
                $groups[$key]['average'] = self::getAverage($value['replies'],$value['amount']);
            }else{
                foreach ($value['replies'] as $keyReply =>  $reply){
                    $groups[$key]['replies'][$keyReply]['percentage'] = (100 * intval($reply['repeated'])) / intval($value['amount']);
                }
            }
        }
        return $groups;
    }


    private static function getAverage($data,$amount): int|float
    {
        $sum = 0;
        foreach ($data as $reply){
            $sum += $reply['reply'] * $reply['repeated'];
        }
        return $sum / $amount;
    }

    private static function getAmountReplies($data):int|float
    {
        $amount = 0;
        foreach ($data as $reply){
            $amount += $reply['repeated'];
        }
        return $amount;
    }
}