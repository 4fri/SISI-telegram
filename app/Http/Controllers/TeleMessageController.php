<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeleMessageController extends Controller
{

    public function sendTelegram(Request $request)
    {
        $request->validate([
            'send_group' => ['required'],
            'sender_message' => ['nullable'],
            'sender_file' => ['required', 'max:2048'],
        ]);

        $botToken = env('TELEGRAM_BOT_TOKEN');
        $sendMessage = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $sendPhoto = "https://api.telegram.org/bot{$botToken}/sendPhoto";

        try {
            foreach ($request->send_group as $chat_id) {
                $messageText = $this->sendMessage($request->sender_message);

                $response = Http::post($sendMessage, [
                    'chat_id' => $chat_id,
                    'text' => $messageText,
                    'parse_mode' => 'HTML',
                ]);

                $response = $this->sendPhoto($request, $chat_id, $sendPhoto);

                if ($response->successful()) {
                    Toastr::success('Pesan telah terkirim ' . "$chat_id", 'Berhasil');
                } else {
                    Toastr::error('Pesan gagal terkirim ' . "$chat_id", 'Gagal');
                }
            }


            return back();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function sendMessage($sender_message)
    {
        $messageData = [
            'title' => 'Notice!!!',
            'content' => $sender_message,
        ];

        // Membersihkan HTML sebelum digunakan
        $cleanedContent = $this->cleanHTML($messageData['content']);

        $messageText = "{$messageData['title']}\n\n{$cleanedContent}";

        return $messageText;
    }

    private function sendPhoto($request, $chat_id, $sendPhoto)
    {
        $today = date('d-m-Y');

        foreach ($request->file('sender_file') as $file) {
            if ($file) {
                $filename = $file->getClientOriginalName();
                $filePath = 'public/report/' . $today;
                $file->storeAs($filePath, $filename);
                $imageUrl = storage_path("app/{$filePath}/{$filename}");
            }

            // Kirim foto terlampir
            $response = Http::attach(
                'photo',
                file_get_contents($imageUrl),
                'photo.jpg'
            )->post($sendPhoto, [
                'chat_id' => $chat_id,
                'caption' => 'Report Gambar',
                'parse_mode' => 'HTML',
            ]);
        }

        return $response;
    }

    private function cleanHTML($html)
    {
        // Remove "p" tags
        $html = preg_replace('/<\/?p\b[^>]*>/', '', $html);

        // Replace "br" tags with newline
        $html = preg_replace('/<br\s*\/?>/', "\n", $html);

        // Remove "ul" tags and replace "li" tags with newline
        $html = preg_replace('/<\/?ul\b[^>]*>/', '', $html);
        $html = preg_replace('/<\/?li\b[^>]*>/', "\n", $html);

        // Remove "img" tags
        $html = preg_replace('/<\/?img\b[^>]*>/', '', $html);

        return $html;
    }
}
