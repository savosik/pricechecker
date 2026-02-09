<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDomJob;
use App\Models\DomTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DomParserController extends Controller
{
    /**
     * Verify API token from request
     */
    private function verifyToken(Request $request): ?JsonResponse
    {
        $token = $request->bearerToken();
        $expectedToken = config('services.dom_parser.token');
        
        if (!$expectedToken || $token !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return null;
    }

    /**
     * Get next pending task for a worker
     * GET /api/dom-parser/task?worker_id=xxx
     */
    public function getTask(Request $request): JsonResponse
    {
        if ($error = $this->verifyToken($request)) {
            return $error;
        }

        $workerId = $request->query('worker_id');
        
        if (!$workerId) {
            return response()->json(['error' => 'worker_id is required'], 400);
        }

        // Find and claim a pending task atomically
        $task = \DB::transaction(function () use ($workerId) {
            $task = DomTask::where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$task) {
                return null;
            }

            $task->markAsProcessing($workerId);
            return $task;
        });

        if (!$task) {
            return response()->json([
                'task' => null,
                'message' => 'No pending tasks',
            ]);
        }

        Log::info('DomParser: Task assigned', [
            'task_id' => $task->id,
            'worker_id' => $workerId,
            'url' => $task->url,
        ]);

        return response()->json([
            'task' => [
                'id' => $task->id,
                'url' => $task->url,
                'marketplace_code' => $task->marketplace_code,
            ],
        ]);
    }

    /**
     * Submit DOM result from worker
     * POST /api/dom-parser/result
     */
    public function submitResult(Request $request): JsonResponse
    {
        if ($error = $this->verifyToken($request)) {
            return $error;
        }

        $validated = $request->validate([
            'task_id' => 'required|integer|exists:dom_tasks,id',
            'worker_id' => 'required|string',
            'success' => 'required|boolean',
            'dom_content' => 'nullable|string',
            'error_message' => 'nullable|string',
        ]);

        $task = DomTask::findOrFail($validated['task_id']);

        // Verify worker owns this task
        if ($task->worker_id !== $validated['worker_id']) {
            return response()->json(['error' => 'Task not assigned to this worker'], 403);
        }

        if ($validated['success']) {
            $task->markAsCompleted($validated['dom_content'] ?? '');
            
            // Dispatch job to process DOM and extract prices
            ProcessDomJob::dispatch($task)->onQueue($task->marketplace_code);
            
            Log::info('DomParser: Task completed', [
                'task_id' => $task->id,
                'dom_length' => strlen($validated['dom_content'] ?? ''),
            ]);
        } else {
            $task->markAsFailed($validated['error_message'] ?? 'Unknown error');
            
            Log::warning('DomParser: Task failed', [
                'task_id' => $task->id,
                'error' => $validated['error_message'],
            ]);
        }

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'status' => $task->status,
        ]);
    }

    /**
     * Get system status for monitoring
     * GET /api/dom-parser/status
     */
    public function status(Request $request): JsonResponse
    {
        if ($error = $this->verifyToken($request)) {
            return $error;
        }

        $stats = [
            'pending' => DomTask::where('status', 'pending')->count(),
            'processing' => DomTask::where('status', 'processing')->count(),
            'completed' => DomTask::where('status', 'completed')->count(),
            'failed' => DomTask::where('status', 'failed')->count(),
            'total' => DomTask::count(),
        ];

        // Check for stale processing tasks (older than 5 minutes)
        $staleCount = DomTask::where('status', 'processing')
            ->where('started_at', '<', now()->subMinutes(5))
            ->count();

        return response()->json([
            'stats' => $stats,
            'stale_processing' => $staleCount,
            'server_time' => now()->toISOString(),
        ]);
    }

    /**
     * Reset stale processing tasks back to pending
     * POST /api/dom-parser/reset-stale
     */
    public function resetStale(Request $request): JsonResponse
    {
        if ($error = $this->verifyToken($request)) {
            return $error;
        }

        $count = DomTask::where('status', 'processing')
            ->where('started_at', '<', now()->subMinutes(5))
            ->update([
                'status' => 'pending',
                'worker_id' => null,
                'started_at' => null,
            ]);

        Log::info('DomParser: Reset stale tasks', ['count' => $count]);

        return response()->json([
            'success' => true,
            'reset_count' => $count,
        ]);
    }
}
