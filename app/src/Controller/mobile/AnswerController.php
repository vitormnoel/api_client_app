<?php

namespace App\Controller\mobile;

use App\Controller\_common\Essentials;
use App\Entity\Answer;
use App\Entity\QuestionAverage;
use App\Entity\QuestionBoolean;
use App\Entity\QuestionOption;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/answer')]
class AnswerController extends Essentials
{
    #[Route('/reply', name: 'private_answer_reply', methods: ['POST'])]
    public function reply(Request $request, AnswerRepository $repository): JsonResponse
    {
        $answer = new Answer();
        $response = $this->formForEntity($request,AnswerType::class,$answer);
        if(!$response instanceof Answer){
            return $response;
        }

        $answer = $repository->find($answer->getId());

        if($this->validateReply($answer,$request) && !$answer->getSession()->isEnded()){
            $this->getEntityManager()->persist($answer);
            $this->getEntityManager()->flush();
            return $this->response(true,$answer,'Successfully');
        }

        $this->getEntityManager()->remove($answer);
        $this->getEntityManager()->flush();
        return $this->response(false,[],'invalid answer','reply sent incompatible with the question');
    }

    private function validateReply(Answer $answer, Request $request): bool
    {
        $data = $request?->get('answer');
        $reply = array_key_exists('reply',$data) ? $data['reply'] : null;

        if(!$reply) {
            return false;
        }

        $question = $answer->getQuestion();

        if($question instanceof QuestionBoolean){
            if(trim($reply) == "true" ){
                $answer->setData(['value' => true]);
                return true;
            }
            if(trim($reply) == "false" ){
                $answer->setData(['value' => false]);
                return true;
            }
            return false;
        }

        if($question instanceof QuestionOption){
            /** @var QuestionOption $question */
            if(in_array(trim($reply),$question->getOptions())){
                $answer->setData(['value' => trim($reply)]);
                return true;
            }
            return false;
        }

        if($question instanceof QuestionAverage){
            $integer = intval($reply);
            /** @var QuestionAverage $question */
            if($integer >= $question->getInitRange() && $integer <= $question->getEndRange()){
                $answer->setData(['value' => $integer]);
                return true;
            }
            return false;
        }
        return false;
    }
}