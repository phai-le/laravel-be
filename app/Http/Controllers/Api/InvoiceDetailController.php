<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvocieDetail\InvoiceDetailRequest;
use App\Http\Resources\InvoiceDetailResource;
use App\Services\InvoiceDetailService;

class InvoiceDetailController extends Controller
{
    public function __construct(
        protected InvoiceDetailService $service
    ) {}

    /**
     * Display the specified booking invoice detail.
     *
     * @param  int  $booking The ID of the booking.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(int $booking)
    {
        $invoiceDetails =  $this->service->get($booking);

        return InvoiceDetailResource::collection($invoiceDetails);
    }

    /**
     * Create or update the invoice details associated with a booking.
     *
     * @param  \App\Http\Requests\InvocieDetail\InvoiceDetailRequest  $request
     * @param  int  $booking The ID of the booking.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function editSave(InvoiceDetailRequest $request, int $booking)
    {
        $updatedInvoiceDetails = $this->service->upSert($request->validated(), $booking);

        return InvoiceDetailResource::collection($updatedInvoiceDetails);
    }

    /**
     * Remove the specified invoice detail from storage.
     *
     * @param  int  $booking The ID of the booking.
     * @param  int  $invoiceDetailId The ID of the invoice detail to remove.
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $booking, int $invoiceDetailId): \Illuminate\Http\Response
    {
        $this->service->deleteService($booking, $invoiceDetailId);

        return response()->noContent();
    }
}
