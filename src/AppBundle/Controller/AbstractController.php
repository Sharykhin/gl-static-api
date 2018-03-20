<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AbstractController
 * @package AppBundle\Controller
 */
abstract class AbstractController extends Controller
{
    /**
     * @param null $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @param array $meta
     * @return JsonResponse
     */
    public function success(
        $data = null,
        int $status = JsonResponse::HTTP_OK,
        array $context = ['groups' => ['list']],
        array $meta = null,
        array $headers = []
    ) : JsonResponse
    {

        return $this->json(['success' => true, 'data' => $data, 'errors' => null, 'meta' => $meta], $status, $headers, $context);
    }

    /**
     * @param $errors
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    public function badRequest(
        $errors,
        int $status = JsonResponse::HTTP_BAD_REQUEST,
        array $headers = [],
        array $context = []
    ) : JsonResponse
    {

        return $this->json(['success' => false, 'data' => null, 'errors' => $errors, 'meta' => null], $status, $headers, $context);
    }

}
