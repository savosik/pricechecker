<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DomTask;
use Illuminate\Console\Command;

class CleanDomTasksCommand extends Command
{
    protected $signature = 'dom-tasks:clean 
                            {--days=7 : Delete completed/failed tasks older than N days}
                            {--keep-failed : Keep failed tasks for debugging}';

    protected $description = 'Clean old DOM tasks and free disk space';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $keepFailed = (bool) $this->option('keep-failed');

        $this->info("Cleaning DOM tasks older than {$days} days...");

        // 1. Clear dom_content from all completed tasks (in case some were missed)
        $cleared = DomTask::where('status', 'completed')
            ->whereNotNull('dom_content')
            ->update(['dom_content' => null]);
        $this->line("  Cleared dom_content from {$cleared} completed tasks");

        // 2. Clear dom_content from failed tasks
        if (!$keepFailed) {
            $clearedFailed = DomTask::where('status', 'failed')
                ->whereNotNull('dom_content')
                ->update(['dom_content' => null]);
            $this->line("  Cleared dom_content from {$clearedFailed} failed tasks");
        }

        // 3. Delete old completed tasks
        $deletedCompleted = DomTask::where('status', 'completed')
            ->where('completed_at', '<', now()->subDays($days))
            ->delete();
        $this->line("  Deleted {$deletedCompleted} old completed tasks");

        // 4. Delete old failed tasks
        if (!$keepFailed) {
            $deletedFailed = DomTask::where('status', 'failed')
                ->where('completed_at', '<', now()->subDays($days))
                ->delete();
            $this->line("  Deleted {$deletedFailed} old failed tasks");
        }

        // 5. Reset stale processing tasks (stuck > 10 minutes)
        $resetStale = DomTask::where('status', 'processing')
            ->where('started_at', '<', now()->subMinutes(10))
            ->update([
                'status' => 'pending',
                'worker_id' => null,
                'started_at' => null,
            ]);
        if ($resetStale > 0) {
            $this->warn("  Reset {$resetStale} stale processing tasks back to pending");
        }

        $remaining = DomTask::count();
        $this->info("Done! Remaining tasks: {$remaining}");

        return self::SUCCESS;
    }
}
