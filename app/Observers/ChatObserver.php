<?php

namespace App\Observers;

use App\Notifications\NewChat;
use App\ChatMessage;
use App\User;

class ChatObserver
{
	public function created(ChatMessage $chat) {
		$author = $chat->user;
		$users = User::all();
		foreach ($users as $user) {
			$user->notify(new NewChat($chat, $author));
		}
	}
}
