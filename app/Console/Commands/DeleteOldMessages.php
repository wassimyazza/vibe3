<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Carbon\Carbon;

class DeleteOldMessages extends Command
{
    protected $signature = 'messages:delete-old';
    protected $description = 'Delete messages older than 24 hours for users who have enabled this feature';

    public function handle()
    {
        $users = User::where('delete_after_24h', true)->get(); // Get users who have enabled the setting
        
        foreach ($users as $user) {
            $messages = $user->messages() // Assuming you have a relationship set up
                            ->whereNull('deleted_at') // Only check messages that haven't been deleted
                            ->where('created_at', '<', Carbon::now()->subHours(24))
                            ->get();

            foreach ($messages as $message) {
                $message->update(['deleted_at' => Carbon::now()]); // Mark the message as deleted
            }
        }

        $this->info('Old messages deleted successfully.');
    }
}
