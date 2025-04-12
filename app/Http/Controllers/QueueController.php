<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\QueueUser;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function listQueues()
    {
        $queues = Queue::all();
        return response()->json($queues);
    }

    public function joinQueue(Request $request, $queueId)
    {
        $user = Auth::user();
        $queue = Queue::findOrFail($queueId);

        // Проверка на существующую запись
        if ($user->queues()->where('queue_id', $queueId)->exists()) {
            return response()->json(['error' => 'User already in queue'], 409);
        }

        $lastPosition = $queue->users()->max('position') ?? 0;
        $newPosition = $lastPosition + 1;

        $queueUser = QueueUser::create([
            'user_id' => $user->id,
            'queue_id' => $queue->id,
            'position' => $newPosition,
            'status' => 'waiting',
        ]);

        return response()->json([
            'message' => 'Successfully joined queue',
            'position' => $newPosition,
            'queue' => $queue->name
        ], 201);
    }

    public function getStatus()
    {
        $user = Auth::user();
        $activeQueue = $user->queueUsers()
            ->with('queue')
            ->where('status', 'waiting')
            ->latest()
            ->first();

        if (!$activeQueue) {
            return response()->json(['message' => 'No active queue'], 404);
        }

        return response()->json([
            'queue' => $activeQueue->queue->name,
            'position' => $activeQueue->position,
            'status' => $activeQueue->status
        ]);
    }

    public function cancelQueue($queueId)
    {
        $user = Auth::user();
        $queueUser = QueueUser::where('user_id', $user->id)
            ->where('queue_id', $queueId)
            ->firstOrFail();

        $queueUser->delete();

        return response()->json(['message' => 'Queue cancelled']);
    }
}