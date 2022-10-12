<?php

namespace App\Controller\admin;

use App\Attribute\QuestionAttribute;
use App\Controller\_common\Essentials;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Interface\QuestionInterface;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/question')]
class QuestionController extends Essentials
{

    #[Route('/create', name: 'private_admin_question_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->request->all('question');
        $question = array_key_exists('type_answer',$data) ? QuestionAttribute::getEntity($data['type_answer']) : null;

        if(!$question) {
            return throw new BadRequestHttpException('Invalid or missing question type');
        }

        $response = $this->formForEntity($request, QuestionType::class,$question);

        if (!$response instanceof QuestionInterface) {
            return $response;
        }
        return $this->response(true, $response, 'Successfully');
    }

    #[Route('/list/{flow}', name: 'private_admin_question_list_flow', methods: ['GET'])]
    public function list(string $flow, QuestionRepository $repository): JsonResponse
    {
        $questions = $repository->findForFlow($flow);

        if(!$questions) {
            return throw new NotFoundHttpException('Question not found');
        }

//        $questions = $this->orderQuestion($questions);

        return $this->response(true, $questions, 'Successfully');
    }

    #[Route('/view/{id}', name: 'private_admin_question_view', methods: ['GET'])]
    public function view(string $id, QuestionRepository $repository): JsonResponse
    {
        $question = $repository->find($id);

        if(!$question) {
            return throw new NotFoundHttpException('Question not found');
        }

        return $this->response(true, $question, 'Successfully');
    }

    #[Route('/delete/{id}', name: 'private_admin_question_delete', methods: ['DELETE'])]
    public function delete(string $id, QuestionRepository $repository): JsonResponse
    {
        $question = $repository->find($id);

        if(!$question) {
            return throw new NotFoundHttpException('Question not found');
        }
        $this->getEntityManager()->remove($question);
        $this->getEntityManager()->flush();

        return $this->response(true, $question, 'Successfully');
    }

    #[Route('/edit/{id}', name: 'private_admin_question_edit', methods: ['POST'])]
    public function edit(string $id, Request $request, QuestionRepository $repository): JsonResponse
    {
        $question = $repository->find($id);

        if(!$question) {
            return throw new NotFoundHttpException('Question not found');
        }

        $response = $this->formForEntity($request, QuestionType::class,$question);

        if (!$response instanceof QuestionInterface) {
            return $response;
        }
        return $this->response(true, $response, 'Successfully');
    }

    private function orderQuestion(array $questions)
    {
        $order = [];

        /** @var Question $question */
        foreach ($questions as $question){
            $order[$question->getType()][] = $question;
       }
        return array_merge($order['question_boolean'] ?? [],$order['question_option'] ?? [],$order['question_average'] ?? []);
    }
}

