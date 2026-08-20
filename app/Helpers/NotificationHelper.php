<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Notification;

class NotificationHelper
{
    public static function notifyVideoSubmission($sender): void
    {
        $hrUsers = User::where('role', 'hr')->where('status', 'active')->get();
        $senderName = $sender->full_name ?? $sender->name;

        foreach ($hrUsers as $hr) {
            Notification::create([
                'user_id' => $hr->id,
                'sender_id' => $sender->id,
                'type' => 'video_submission',
                'title' => 'Video Submission Baru',
                'message' => "{$senderName} telah mengumpulkan video English Today.",
                'data' => [
                    'sender_name' => $senderName,
                    'sender_email' => $sender->email,
                ],
            ]);
        }
    }

    public static function notifyMaterialUpload($uploader, $materi): void
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        $uploaderName = $uploader->full_name ?? $uploader->name;

        foreach ($employees as $employee) {
            Notification::create([
                'user_id' => $employee->id,
                'sender_id' => $uploader->id,
                'type' => 'material_upload',
                'title' => 'Materi Baru Diupload',
                'message' => "{$uploaderName} telah mengupload materi baru: {$materi->title}.",
                'data' => [
                    'uploader_name' => $uploaderName,
                    'materi_title' => $materi->title,
                    'materi_id' => $materi->id,
                ],
            ]);
        }
    }

    public static function notifyChallengeUpload($uploader, $challenge): void
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        $uploaderName = $uploader->full_name ?? $uploader->name;

        foreach ($employees as $employee) {
            Notification::create([
                'user_id' => $employee->id,
                'sender_id' => $uploader->id,
                'type' => 'challenge_upload',
                'title' => 'Video Challenge Baru',
                'message' => "{$uploaderName} telah membuat video challenge baru: {$challenge->title}.",
                'data' => [
                    'uploader_name' => $uploaderName,
                    'challenge_title' => $challenge->title,
                    'challenge_id' => $challenge->id,
                ],
            ]);
        }
    }

    public static function notifyQuizUpload($uploader, $quiz): void
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        $uploaderName = $uploader->full_name ?? $uploader->name;

        foreach ($employees as $employee) {
            Notification::create([
                'user_id' => $employee->id,
                'sender_id' => $uploader->id,
                'type' => 'quiz_upload',
                'title' => 'Quiz Baru Tersedia',
                'message' => "{$uploaderName} telah membuat quiz baru: {$quiz->title}.",
                'data' => [
                    'uploader_name' => $uploaderName,
                    'quiz_title' => $quiz->title,
                    'quiz_id' => $quiz->id,
                ],
            ]);
        }
    }
}
