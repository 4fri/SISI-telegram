<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeleMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'send_group' => ['required'],
            'sender_message' => ['nullable'],
            'sender_file' => ['required', 'mimes:png,jpg,jpeg,pdf', 'max:2048'],
        ]);

        $today = date('d-m-Y');

        if ($request->hasFile('sender_file')) {
            $file = $request->file('sender_file');
            $filename = $file->getClientOriginalName();
            $filePath = 'public/report/' . $today;
            $file->storeAs($filePath, $filename);
            $imageUrl = storage_path("app/{$filePath}/{$filename}");
        }

        $botToken = env('APP_BOT_TELEGRAM');
        $sendMessage = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $sendPhoto = "https://api.telegram.org/bot{$botToken}/sendPhoto";

        $messageData = [
            'title' => 'Notice!!!',
            'content' => $request->sender_message,
        ];

        // Membersihkan HTML sebelum digunakan
        $cleanedContent = $this->cleanHTML($messageData['content']);

        $messageText = "{$messageData['title']}\n\n{$cleanedContent}";

        try {
            foreach ($request->send_group as $chat_id) {
                // Kirim pesan dengan foto terlampir
                $response = Http::post($sendMessage, [
                    'chat_id' => $chat_id,
                    'text' => $messageText,
                    'parse_mode' => 'HTML',
                ]);

                if ($request->hasFile('sender_file')) {
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
