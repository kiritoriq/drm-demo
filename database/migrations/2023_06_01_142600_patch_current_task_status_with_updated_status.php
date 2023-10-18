<?php

use Domain\Shared\Ticket\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tasks = Task::query()
            ->whereNotNull('status')
            ->get();

        foreach ($tasks as $task) {
            if ($task->status === 'pending') {
                $task->update([
                    'status' => 'new'
                ]);
            }

            if ($task->status === 'completed') {
                $task->update([
                    'status' => 'progress_completed'
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
