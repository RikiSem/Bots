<?php


namespace App\Http\Bots\RandomBot;

use App\Http\Bots\RandomBot\src\Controllers\AudioController;
use App\Http\Bots\RandomBot\src\Controllers\BaseController;
use App\Http\Bots\RandomBot\src\Controllers\LoggerController;
use App\Http\Bots\RandomBot\src\Controllers\PaymentController;
use App\Http\Bots\RandomBot\src\Controllers\PhotoController;
use App\Http\Bots\RandomBot\src\Controllers\UploadController;
use App\Http\Bots\RandomBot\src\Controllers\UserController;
use App\Http\Bots\RandomBot\src\Controllers\VideoController;
use App\Http\Bots\RandomBot\src\Controllers\WaifuController;
use App\Http\Bots\RandomBot\src\Models\Blacklist;
use App\Http\Bots\RandomBot\src\Models\Subscriber;
use App\Http\Bots\RandomBot\src\Models\User;
use App\Http\Bots\RandomBot\src\Replybtns;
use App\Http\Bots\RandomBot\src\Reps\BlacklistRep;
use App\Http\Bots\RandomBot\src\Reps\PostBlacklistRep;
use App\Http\Bots\RandomBot\src\Reps\PostRep;
use App\Http\Bots\RandomBot\src\Reps\SubscriberRep;
use App\Http\Bots\RandomBot\src\Reps\UserRep;
use App\Http\Bots\RandomBot\src\Texts;
use App\Http\Reps\ChatRep;
use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class RandomBot
{
    public const BOT_NAME = 'RandomBot';
    protected TelegraphBot $bot;
    protected PhotoController $photoController;
    protected VideoController $videoController;
    protected PaymentController $paymentController;
    protected LoggerController $loggerController;
    protected UserController $userController;
    protected AudioController $audioController;
    protected UploadController $uploadController;
    protected SubscriberRep $subscriberRep;
    protected ChatRep $chatRep;
    protected UserRep $userRep;
    function __construct(TelegraphBot $bot)
    {
        $this->bot = $bot;
        $this->photoController = new PhotoController($this->bot);
        $this->videoController = new VideoController($this->bot);
        $this->paymentController = new PaymentController($this->bot);
        $this->loggerController = new LoggerController($this->bot);
        $this->audioController = new AudioController($this->bot);
        $this->userController = new UserController();
        $this->uploadController = new UploadController();

        $this->subscriberRep = new SubscriberRep();
        $this->chatRep = new ChatRep();
        $this->userRep = new UserRep();
    }
    public function handler(array $request)
    {
        file_put_contents('request.json', json_encode($request));

        if (isset($request['message'])) {
            $this->handleMessage($request['message']);
        } else if (isset($request['pre_checkout_query'])) {
            $this->handlePreCheckoutQuery($request['pre_checkout_query']);
        }  else if (isset($request['callback_query'])) {
            $this->handleCallbackQuery($request['callback_query']);
        }
    }

    public function handleMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $messageText = $message['text'] ?? '';
        $username = $message['chat']['username'] ?? 'Неизвестный';
        $chat = $this->chatRep->firstOrCreate($chatId,$username,$this->bot->id);
        $user = $this->userRep->firstOrCreate($chatId,$username);

        if (isset($message['text'])) {
            $user->blockLoadAllEntities();
        }

        $defaultBtns = Replybtns::defaultBtns();

        $sub = $this->subscriberRep->getSubscriptionByUserId($user->id);
        $subIsActive = $this->subscriberRep->userCanUsePaymentContent($sub);
        $showNotification = rand(1, 10) > 5;
        $showAdds =  rand(1, 10) > 5;
        $inBan = $this->userController->isBlocked($chat->chat_id);

        if ($user->is_admin || $subIsActive) {
            $defaultBtns = Replybtns::premiumBtns();
            if ($user->is_admin) {
                $defaultBtns = Replybtns::adminBtns();
            }
            $showNotification = false;
            $showAdds = false;
        }

        if ($inBan !== null && $messageText !== Replybtns::UNBAN_USER_TEXT) {
            $chat
                ->html(sprintf("Вы заблокированы\nПричина - %s", $inBan->reason))
                ->replyKeyboard(Replybtns::unBanBtns())
                ->send();
        } else {
            try {
                switch ($messageText) {
                    case Replybtns::START_COMMAND:
                        $chat
                            ->html(Texts::greatingText($username))
                            ->replyKeyboard($defaultBtns)
                            ->send();
                        break;
                    case Replybtns::RANDOM_FOTO_TEXT:
                        if ($showNotification && !$subIsActive) {
                            $this->sendNotification($chat);
                        }
                        $this->photoController->send($chat, !$subIsActive);
                        $this
                            ->loggerController
                            ->log(sprintf('Пользователь @%s(%s) смотрит фото', $user->username, $user->telegram_id));
                        break;
                    case Replybtns::RANDOM_VIDEO_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $this->videoController->send($chat, !$subIsActive);
                            $this
                                ->loggerController
                                ->log(sprintf('Пользователь @%s(%s) смотрит видео', $user->username, $user->telegram_id));
                        } else {
                            throw new Exception('У вас не оформлена подписка или она истекла');
                        }
                        break;
                    case Replybtns::RANDOM_AUDIO_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $this->audioController->send($chat, !$subIsActive);
                            $this
                                ->loggerController
                                ->log(sprintf('Пользователь @%s(%s) слушает аудио', $user->username, $user->telegram_id));
                        } else {
                            throw new Exception('У вас не оформлена подписка или она истекла');
                        }
                        break;
                    case Replybtns::RANDOM_VIDEO_NOTE_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $this->videoController->sendNote($chat, !$subIsActive);
                            $this
                                ->loggerController
                                ->log(sprintf('Пользователь @%s(%s) смотрит кружок', $user->username, $user->telegram_id));
                        } else {
                            throw new Exception('У вас не оформлена подписка или она истекла');
                        }
                        break;
                    case Replybtns::LOAD_VIDEO_NOTE_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $chat
                                ->html('Тогда запиши 1 кружок')
                                ->send();
                            $user->can_send_video_note = true;
                            $user->update();
                        } else {
                            throw new Exception('У вас не оформлена подписка или она истекла');
                        }
                        break;
                    case Replybtns::LOAD_AUDIO_TEXT:
                        $chat
                            ->html('Тогда отправь 1 аудио')
                            ->send();
                        $user->can_send_audio = true;
                        $user->update();
                        break;
                    case Replybtns::LOAD_FOTO_TEXT:
                        $chat
                            ->html('Тогда отправь 1 фото')
                            ->send();
                        $user->can_send_photo = true;
                        $user->update();
                        break;
                    case Replybtns::LOAD_VIDEO_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $chat
                                ->html('Тогда отправь 1 видео')
                                ->send();
                            $user->can_send_video = true;
                            $user->update();
                        } else {
                            throw new Exception('У вас не оформлена подписка или она истекла');
                        }
                        break;
                    case Replybtns::UNBAN_USER_TEXT:
                        $chat->invoice(Blacklist::TITLE)
                            ->description(Blacklist::DESCRIPTION)
                            ->currency(Blacklist::CURRENCY)
                            ->payload(Blacklist::PAYLOAD)
                            ->addItem(
                                Blacklist::CURRENCY,
                                Blacklist::PRICE
                            )
                            ->send();
                            break;
                    case Replybtns::BUY_COMMAND:
                    case Replybtns::BUY_PREM_TEXT:
                        if ($subIsActive || $user->is_admin) {
                            $chat
                                ->message('У вас уже оформлена подписка')
                                ->send();
                        } else {
                            $chat
                                ->html(sprintf(Subscriber::DESCRIPTION . "\nПодписка оформляется на <strong>месяц</strong> и стоит <strong><u>%s</u></strong> Telegram Start", Subscriber::PRICE))
                                ->send();
                            $chat->invoice(Subscriber::TITLE)
                                ->description(Subscriber::DESCRIPTION)
                                ->currency(Subscriber::CURRENCY)
                                ->payload(Subscriber::PAYLOAD)
                                ->addItem(
                                    Subscriber::CURRENCY,
                                    Subscriber::PRICE
                                )
                                ->send();
                        }
                        break;
                    case Replybtns::BAN_USER_TEXT:
                        if ($user->is_admin) {
                        $chat
                            ->message('Введи id юзера')
                            ->send();
                        $user->on_post_ban = true;
                        $user->update();
                        }
                        break;
                    case Replybtns::RULES_COMMAND:
                        $chat
                            ->message('За нарушение правил может последовать блокировка')
                            ->send();
                        $chat->message(implode("\n", Texts::RULES))
                            ->send();
                        break;
                    case Replybtns::TERMS_COMMAND:
                        $chat
                            ->html(Texts::TERMS)
                            ->send();
                        break;
                    default:
                        try {
                            if (isset($message['text'])) {
                                if ($user->canBanPost()) {
                                    $this->userController->blockUser($message['text']);
                                    $user->blockPostBan();
                                    $chat
                                        ->message(message: 'Юзер заблокирован')
                                        ->replyKeyboard($defaultBtns)
                                        ->send();
                                } else {
                                    $chat
                                        ->message('Что?')
                                        ->replyKeyboard($defaultBtns)
                                        ->send();
                                }
                            }
                            if (isset($message['successful_payment'])) {
                                $this->successfulPayment($message['successful_payment'], $chat, $user);
                            }
                            if (isset($message['photo'])) {
                                if ($user->canLoadPhoto()) {
                                    try {
                                        $this->loadPhoto($message['photo'][0], $chat->chat_id);
                                        $user->unblockLoadPhoto();
                                        $chat
                                            ->message('Загружено, спасибо!')
                                            ->send();
                                        $this
                                            ->loggerController
                                            ->log(sprintf('Пользователь @%s(%s) загрузил фото', $user->username, $user->telegram_id));
                                    } catch (Exception $e) {
                                        $this
                                            ->loggerController
                                            ->log($e->getMessage());
                                    }
                                } else {
                                    throw new Exception(Texts::pressBtn(Replybtns::LOAD_FOTO_TEXT));
                                }
                            }
                            if (isset($message['video'])) {
                                if ($user->canLoadVideo()) {
                                    try {
                                        $this->loadVideo($message['video'], $chat->chat_id);
                                        $user->blockLoadVideo();
                                        $chat
                                            ->message('Загружено, спасибо!')
                                            ->send();
                                        $this
                                            ->loggerController
                                            ->log(sprintf('Пользователь @%s(%s) загрузил видео', $user->username, $user->telegram_id));
                                    } catch (Exception $e) {
                                        $this
                                            ->loggerController
                                            ->log($e->getMessage());
                                    }
                                } else {
                                    throw new Exception(Texts::pressBtn(Replybtns::LOAD_VIDEO_TEXT));
                                }
                            }
                            if (isset($message['video_note'])) {
                                if ($user->canLoadVideoNote()) {
                                    try {
                                        $this->loadVideoNote($message['video_note'], $chat->chat_id);
                                        $user->blockLoadVideoNote();
                                        $chat
                                            ->message('Загружено, спасибо!')
                                            ->send();
                                        $this
                                            ->loggerController
                                            ->log(sprintf('Пользователь @%s(%s) загрузил кружок', $user->username, $user->telegram_id));
                                    } catch (Exception $e) {
                                        $this
                                            ->loggerController
                                            ->log($e->getMessage());
                                    }
                                } else {
                                    throw new Exception(Texts::pressBtn( Replybtns::LOAD_VIDEO_NOTE_TEXT));
                                }
                            }
                            if (isset($message['audio'])) {
                                if ($user->canLoadAudio()) {
                                    try {
                                        $this->loadAudio($message['audio'], $chat->chat_id);
                                        $user->blockLoadAudio();
                                        $chat
                                            ->message('Загружено, спасибо!')
                                            ->send();
                                        $this
                                            ->loggerController
                                            ->log(sprintf('Пользователь @%s(%s) загрузил аудио', $user->username, $user->telegram_id));
                                    } catch (Exception $e) {
                                        $this
                                            ->loggerController
                                            ->log($e->getMessage());
                                    }
                                } else {
                                    throw new Exception(Texts::pressBtn(Replybtns::LOAD_AUDIO_TEXT));
                                }
                            }
                            if (isset($message['voice'])) {
                                if ($user->canLoadAudio()) {
                                    try {
                                        $this->loadAudio($message['voice'], $chat->chat_id);
                                        $user->blockLoadAudio();
                                        $chat
                                            ->message('Загружено, спасибо!')
                                            ->send();
                                        $this
                                            ->loggerController
                                            ->log(sprintf('Пользователь @%s(%s) загрузил голосовое', $user->username, $user->telegram_id));
                                    } catch (Exception $e) {
                                        $this
                                            ->loggerController
                                            ->log($e->getMessage());
                                    }
                                } else {
                                    throw new Exception(Texts::pressBtn(Replybtns::LOAD_AUDIO_TEXT));
                                }
                            }
                        } catch (Exception $exception) {
                            throw new Exception($exception->getMessage());
                        }
                        break;
                }
            } catch (Exception $e) {
                $chat
                    ->message($e->getMessage())
                    ->replyKeyboard($defaultBtns)
                    ->send();
            }
        }
    }

    public function loadAudio(array $audio, int $chatId)
    {
        $this->audioController->save($audio['file_id'], $chatId, PostRep::AUDIO_TYPE);
    }

    public function loadVideoNote(array $note, int $chatId) 
    {
        $this->videoController->save($note['file_id'], $chatId, PostRep::VIDEO_NOTE_TYPE);
    }

    public function loadVideo(array $video, int $chatId) 
    {
        $this->videoController->save($video['file_id'], $chatId, PostRep::VIDEO_TYPE);
    }

    public function loadPhoto(array $photo, int $chatId) 
    {
        $this->photoController->save($photo['file_id'], $chatId, PostRep::PHOTO_TYPE);
    }

    public function handlePreCheckoutQuery(array $preCheckoutQuery)
    {
        $this->paymentController->answerPreCheckoutQuery($preCheckoutQuery['id']);
    }

    public function successfulPayment(array $payment, TelegraphChat $chat, User $user)
    {
        switch ($payment['invoice_payload']) {
            case Subscriber::PAYLOAD:
                $this->subscriberRep->setSubscription($user->id);
                $chat
                    ->message('Поздравляем, подписка активирована на месяц!')
                    ->replyKeyboard(Replybtns::premiumBtns())
                    ->send();
                $this
                    ->loggerController
                    ->log(sprintf('Пользователь @%s(%s) оформил подписку', $user->username, $user->telegram_id));
                break;
            case Blacklist::PAYLOAD:
                $this->userController->unBlockUser($chat->chat_id);
                $chat
                    ->message('Вы разбанены')
                    ->send();
                break;
        }
    }

    public function handleCallbackQuery(array $callbackQuery)
    {
        $chat = $this->chatRep->getChat($callbackQuery['from']['id'], $this->bot->id);
        PostBlacklistRep::banPost($callbackQuery['data'], $chat->chat_id);
        Http::post(sprintf(BaseController::URL . 'answerCallbackQuery', $this->bot->token), [
            'callback_query_id' => $callbackQuery['id'],
            'text' => sprintf('Пост %s заблокирован', $callbackQuery['data']),
            'show_alert' => false
        ]);
    }

    public function sendNotification(TelegraphChat $chat)
    {
        $chat
            ->html(sprintf("❗❗❗\n%s\n❗❗❗", Texts::NOTIFICATION[array_rand(Texts::NOTIFICATION)]))
            ->send();
    }
}
