<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use App\Models\Queue;
use App\Models\QueueUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class QueueController extends Controller
{
    public function listQueues()
    {
        $queues = Queue::all();
        return response()->json($queues);
    }

    public function joinQueue(Request $request, $queueId)
    {
        try {
            $user = Auth::user();
            $queue = Queue::findOrFail($queueId);

            if ($user->queues()->where('queue_id', $queueId)->exists()) {
                return response()->json(['error' => 'Вы уже записаны в эту очередь'], 409);
            }

            $lastPosition = $queue->users()->max('position') ?? 0;
            $newPosition = $lastPosition + 1;

            $queueUser = QueueUser::create([
                'user_id' => $user->id,
                'queue_id' => $queue->id,
                'position' => $newPosition,
                'status' => 'waiting',
            ]);

            
            Event::dispatch(new QueueUpdated($queue->id, $newPosition));

            return response()->json([
                'message' => 'Вы успешно записаны в очередь',
                'position' => $newPosition,
                'total_in_queue' => $newPosition,
                'queue' => $queue->name
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка записи в очередь',
                'details' => $e->getMessage()
            ], 500);
        }
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
            return response()->json(['message' => 'Нет активных записей в очередь'], 404);
        }

        return response()->json([
            'queue' => $activeQueue->queue->name,
            'position' => $activeQueue->position,
            'total_in_queue' => $activeQueue->queue->users()->count(),
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

     
        Event::dispatch(new QueueUpdated($queueId, $queueUser->position));

        return response()->json([
            'message' => 'Запись в очередь отменена',
            'canceled_position' => $queueUser->position
        ]);
    }
}