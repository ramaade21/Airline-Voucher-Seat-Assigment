<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckVoucherRequest;
use App\Http\Requests\GenerateVoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Vouchers", description="Voucher seat assignment endpoints")
 */
class VoucherController extends Controller
{
    public function __construct(
        private readonly VoucherService $voucherService,
    ) {}

    /**
     * POST /api/v1/check
     *
     * Check whether vouchers already exist for a given flight number + date.
     *
     * @OA\Post(
     *     path="/api/v1/check",
     *     tags={"Vouchers"},
     *     summary="Check whether vouchers already exist for a flight",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"flightNumber","date"},
     *             @OA\Property(property="flightNumber", type="string", example="GA102"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-12")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Existence check result",
     *         @OA\JsonContent(@OA\Property(property="exists", type="boolean"))
     *     )
     * )
     */
    public function check(CheckVoucherRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $exists = $this->voucherService->exists(
            $validated['flightNumber'],
            $validated['date'],
        );

        return response()->json([
            'exists' => $exists,
        ]);
    }

    /**
     * POST /api/v1/generate
     *
     * Generate exactly three unique random voucher seats for a flight.
     *
     * @OA\Post(
     *     path="/api/v1/generate",
     *     tags={"Vouchers"},
     *     summary="Generate three unique random voucher seats",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","id","flightNumber","date","aircraft"},
     *             @OA\Property(property="name", type="string", example="Sarah"),
     *             @OA\Property(property="id", type="string", example="98123"),
     *             @OA\Property(property="flightNumber", type="string", example="GA102"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-12"),
     *             @OA\Property(property="aircraft", type="string", example="Airbus 320")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Seats generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="seats", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=409, description="Vouchers already exist for this flight/date"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function generate(GenerateVoucherRequest $request): JsonResponse
    {
        $voucher = $this->voucherService->generate($request->validated());

        return (new VoucherResource($voucher))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
