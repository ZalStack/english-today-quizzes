<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use App\Models\Quiz;
use App\Models\VideoChallenge;
use App\Traits\ApiResponseTrait;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $data = [
            'total_divisions' => Division::count(),
            'total_employees' => User::where('role', 'employee')->count(),
            'total_quizzes' => Quiz::count(),
            'total_video_challenges' => VideoChallenge::count(),
        ];
        return $this->success($data);
    }
}
