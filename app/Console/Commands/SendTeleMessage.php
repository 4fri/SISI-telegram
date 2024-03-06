<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTeleMessage extends Command
{
    protected $signature = 'send:tele-message';
    protected $description = 'Send telegram message every 1 minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    // Pada file SendTeleMessage.php
    public function handle()
    {
        $teleMessageController = app(\App\Http\Controllers\TeleMessageController::class);

        // Simulate a Request object with necessary data
        $request = new \Illuminate\Http\Request([
            'send_group' => '-1002011654734', // Ganti dengan chat ID yang sesuai
            'sender_message' => 'Your automated message content',
            // 'sender_file' => null, // Ganti dengan path file jika diperlukan
        ]);

        try {
            $teleMessageController->sendMessage($request);
            $this->info('Telegram message sent successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Validation error: ' . $e->getMessage());
        }
    }
}
