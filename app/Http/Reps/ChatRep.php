<?php


namespace App\Http\Reps;

use App\Models\TelegraphBot;
use App\Models\TelegraphChat;

class ChatRep
{
    public function isChatExist(int|string $chatId, int $botId): bool
    {
        return !TelegraphChat::where('chat_id', '=', $chatId)
            ->where('telegraph_bot_id', '=', $botId)
            ->get()
            ->isEmpty();
    }

    public function createChat(string $userName, int $chatId, int $botId): TelegraphChat
    {
        $chat = new TelegraphChat();
        $chat->name = $userName;
        $chat->chat_id = $chatId;
        $chat->telegraph_bot_id = $botId;
        $chat->save();
        return $chat;
    }

    public function getChat(int|string $chatId, int $botId): TelegraphChat
    {
        return TelegraphChat::where('chat_id', '=', $chatId)
            ->where('telegraph_bot_id', '=', $botId)
            ->first();
    }

    public function firstOrCreate(int $tgId, string $username, int $botId): TelegraphChat
    {
        return TelegraphChat::firstOrCreate([
            'chat_id' => (string)$tgId,
            'telegraph_bot_id' => $botId
        ], [
            'name' => $username,
        ]);
    }
}
