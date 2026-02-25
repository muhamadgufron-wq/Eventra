<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $clients = Client::query()
            ->with('package')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('bride_name', 'ilike', "%{$s}%")
                  ->orWhere('groom_name', 'ilike', "%{$s}%")
                  ->orWhere('bride_phone', 'ilike', "%{$s}%")
                  ->orWhere('groom_phone', 'ilike', "%{$s}%");
            }))
            ->latest()
            ->paginate($request->input('per_page', 15));

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Client $client): ClientResource
    {
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client): ClientResource
    {
        $client->update($request->validated());

        return new ClientResource($client);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(['message' => 'Client berhasil dihapus.']);
    }
}
