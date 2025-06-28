<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ActivityResource;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class SystemActivitiesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $activities = Activity::with('causer')
            ->latest()
            ->paginate(20);

        return ActivityResource::collection($activities);
    }
}
