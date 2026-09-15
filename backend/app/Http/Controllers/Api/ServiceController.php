<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'services:index:' . md5($request->fullUrl());

        $paginator = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Service::query()->active();

            if ($request->has('category')) {
                $query->byCategory($request->string('category'));
            }

            return $query->orderBy('category')->orderBy('price')->paginate(perPage: 12);
        });

        return ServiceResource::collection($paginator);
    }

    public function show(string $service): ServiceResource
    {
        $cacheKey = "services:show:{$service}";

        $model = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($service) {
            $found = Service::query()->active()->find($service);

            if (! $found) {
                throw new ModelNotFoundException('Servicio no encontrado.');
            }

            return $found;
        });

        return new ServiceResource($model);
    }
}