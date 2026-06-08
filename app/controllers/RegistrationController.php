<?php

namespace App\Controllers;
use App\Services\RegistrationService;
use App\Requests\RegistrationRequest;
use Core\Controller;
use Core\Response;
use Core\QueryBuilder;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationService $service
    ) {}

    public function confirm(RegistrationRequest $request): Response
    {
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            return Response::json(
                [
                    'status' => 'success',
                ]
            );
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function complete(RegistrationRequest $request): Response
    {
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            $this->service->registrationUser($request);
            return Response::json(
                [
                    'status' => 'success',
                ]
            );
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
