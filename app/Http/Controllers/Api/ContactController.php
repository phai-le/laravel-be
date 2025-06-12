<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ContactService;
use App\Http\Resources\ContactResources;
use App\Http\Requests\Contact\GetContactRequest;
use App\Http\Requests\Contact\SendMailRequest;

class ContactController extends Controller
{
    public function __construct(
        protected ContactService $contactService,
    ) {}

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->contactService->get($request->all());

        return ContactResources::collection($data);
    }

    /**
     * Summary of show
     * @param int $id
     * @return ContactResources
     */
    public function show(int $id): ContactResources
    {
        $data = $this->contactService->show($id);

        return new ContactResources($data);
    }

    /**
     * Summary of sendMail
     * @param SendMailRequest $request
     * @param int $id
     * @return ContactResources
     */
    public function sendMail(SendMailRequest $request,int $id): ContactResources
    {
        $data = $request->validated();

        $sendMail = $this->contactService->sendMail($data,$id);

        return new ContactResources($sendMail);
    }

    /**
     * Summary of sendContact
     * @param \App\Http\Requests\Contact\SendMailRequest $request
     * @return ContactResources
     */
    public function sendContact(GetContactRequest $request): ContactResources
    {
        $data = $request->validated();

        $sendContact = $this->contactService->sendContact($data);

        return new ContactResources($sendContact);
    }
}
