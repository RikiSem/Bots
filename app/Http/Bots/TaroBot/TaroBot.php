<?php


namespace App\Http\Bots\TaroBot;

use App\Http\Bots\TaroBot\src\PaidMedia;
use App\Http\Bots\TaroBot\src\ReplyBtns;
use App\Http\Bots\TaroBot\src\Reps\AmuletRep;
use App\Http\Bots\TaroBot\src\Reps\PersonalHoroscopeRep;
use App\Http\Bots\TaroBot\src\Reps\SubscriberRep;
use App\Http\Bots\TaroBot\src\Reps\UserRep;
use App\Http\Bots\TaroBot\src\Reps\ZodiacRep;
use App\Http\Bots\TaroBot\src\Socials\Texts;
use App\Http\Bots\TaroBot\src\Models\Subscriber;
use App\Http\Bots\TaroBot\src\Models\User;
use App\Http\Reps\ChatRep;
use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;

class TaroBot
{
    protected ChatRep $chatRep;
    public function __construct()
    {
        $this->chatRep = new ChatRep();
    }
    public const BOT_NAME = 'TaroBot';
    public const FOTO_PATH = '/../../storage/app/public/storage/';
    public function handler(Request $request): void
    {
        try{
            $bot = TelegraphBot::where('token', $request->token)->first();
            if (!is_null($bot)) {
                if (isset($request->message)) {
                    $chatId = $request->message['chat']['id'];
                    $messageText = $request->message['text'];
                    $username = $request->message['chat']['username'];
                    /** @var User $user */
                    /** @var TelegraphChat $chat */
                    if (!$this->chatRep->isChatExist($chatId, $bot->id)) {
                        $chat = $this->chatRep->createChat(
                            $username,
                            $chatId,
                            $bot->id
                        );
                        $user = UserRep::createUser(
                            $chatId,
                            $username
                        );
                    } else {
                        $chat = $this->chatRep->getChat($chatId, $bot->id);
                        $user = UserRep::getUser($chatId);
                    }

                    $sub = SubscriberRep::getSubscriptionByUserId($user->id);
                    $subIsActive = SubscriberRep::userCanUsePaymentContent($sub);
                    $showNotification = rand(1, 10) > 7;
                    $showAdds =  rand(1, 10) > 7;
                    /*$chat
                        ->message($user->set_info_state)
                        ->send();*/

                    switch ((bool)$user->set_info_state) {
                        case false:
                            switch ($messageText) {
                                case ReplyBtns::COMMAND_START:
                                    $chat
                                        ->message(Texts::defaultText($username))
                                        ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                        ->send();
                                    break;
                                case ReplyBtns::ON_MAIN_TEXT:
                                    $chat
                                        ->message(sprintf('Хорошо, %s', $username))
                                        ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                        ->send();
                                    break;
                                case ReplyBtns::COMMAND_PROFILE:
                                case ReplyBtns::SEE_PROFILE_TEXT:
                                    $birthday = Carbon::createFromTimestamp($user->birthday)
                                        ->format('d.m.Y');
                                    $chat
                                        ->html("Вот твой профиль:
                                        \n<strong>Имя</strong> - {$user->name}\n<strong>Возраст</strong> - {$user->age}\n<strong>Пол</strong> - {$user->sex}\n<strong>Дата рождения</strong> - {$birthday}\n<strong>Знак зодиака</strong> - {$user->zodiac_horoscope}\n<strong>Животное в китайском гороскопе</strong> - {$user->chinese_horoscope}
                                        ")
                                        ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                        ->send();
                                    break;
                                case ReplyBtns::COMMAND_HORO_DAY:
                                case ReplyBtns::DAILY_HOROSCOPE_TEXT:
                                    if (!$subIsActive && ($showAdds || $showNotification)) {
                                        if ($showNotification) {
                                            $this->sendNotifiation($chat);
                                        }
                                    }
                                    try {
                                        $zodiac = ZodiacRep::getDailyHoroscopeBySing($user->zodiac_horoscope);
                                        if ($zodiac === null) {
                                            throw new Exception('Ошибка в составлении гороскопа на этот день');
                                        }
                                        $this->sendHoroscope($zodiac->text, $chat);
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_HORO_MONTH:
                                case ReplyBtns::MONTHLY_HOROSCOPE_TEXT:
                                    if (!$subIsActive && ($showAdds || $showNotification)) {
                                        if ($showNotification) {
                                            $this->sendNotifiation($chat);
                                        }
                                    }
                                    try {
                                        $zodiac = ZodiacRep::getMonthlyHoroscopeBySing($user->zodiac_horoscope);
                                        if ($zodiac === null) {
                                            throw new Exception('Ошибка в составлении гороскопа на этот месяц');
                                        }
                                        $this->sendHoroscope($zodiac->text, $chat);
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_HORO_YEAR:
                                case ReplyBtns::YEARLY_HOROSCOPE_TEXT:
                                    try {
                                        if (!$subIsActive && ($showAdds || $showNotification)) {
                                            if ($showNotification) {
                                                $this->sendNotifiation($chat);
                                            }
                                        }
                                        $zodiac = ZodiacRep::getYearlyHoroscopeBySing($user->zodiac_horoscope);
                                        if ($zodiac === null) {
                                            throw new Exception('Ошибка в составлении гороскопа на этот год');
                                        }
                                        $this->sendHoroscope($zodiac->text, $chat);
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_AMULETS:
                                case ReplyBtns::AMULETS_TEXT:
                                    try {
                                        $chat
                                            ->markdown('Перед покупкой оберегов, рекомендуем ознакомиться с [правилами](https://docs.google.com/document/d/1Xkj8WJg_izlJ6eFLanqSg0G1cH4Y5ZKHp9TOZITljR0/edit?usp=sharing) покупки в данном боте')
                                            ->withoutPreview()
                                            ->send();
                                        $chat
                                            //->html("Выбери уровень оберега\nСправка по оберегам и их уровням: /help")
                                            ->html("Выбери уровень оберега")
                                            ->replyKeyboard(ReplyBtns::amuletsLevelsKeyBoard())
                                            ->send();
                                        $user->set_select_amulet_lvl_state = true;
                                        $user->update();
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_PERS_HORO_DAY:
                                case ReplyBtns::PERSONAL_DAILY_HOROSCOPE_TEXT:
                                    try {
                                        if ($subIsActive) {
                                            $this->sendHoroscope(
                                                PersonalHoroscopeRep::getDailyHoroByIserId($user->id)->text,
                                                $chat
                                            );
                                        } else {
                                            $chat
                                                ->message(Texts::NOT_SUBSCRIBER_TEXT)
                                                ->send();
                                            $chat->invoice(Subscriber::TITLE)
                                                ->description(Subscriber::DESCRIPTION)
                                                ->currency(Subscriber::CURRENCY)
                                                ->payload(Subscriber::PAYMENT_PAYLOAD)
                                                ->addItem(
                                                    Subscriber::CURRENCY,
                                                    Subscriber::PAYMENT_COST
                                                )
                                                ->send();
                                        }
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_PERS_HORO_MONTH:
                                case ReplyBtns::PERSONAL_MONTHLY_HOROSCOPE_TEXT:
                                    try {
                                        if ($subIsActive) {
                                            $this->sendHoroscope(
                                                PersonalHoroscopeRep::getMonthlyHoroByUserId($user->id)->text,
                                                $chat
                                            );
                                        } else {
                                            $chat
                                                ->message(Texts::NOT_SUBSCRIBER_TEXT)
                                                ->send();
                                            $chat->invoice(Subscriber::TITLE)
                                                ->description(Subscriber::DESCRIPTION)
                                                ->currency(Subscriber::CURRENCY)
                                                ->payload(Subscriber::PAYMENT_PAYLOAD)
                                                ->addItem(
                                                    Subscriber::CURRENCY,
                                                    Subscriber::PAYMENT_COST
                                                )
                                                ->send();
                                        }
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->send();
                                    }
                                    break;
                                case ReplyBtns::COMMAND_PERS_HORO_YEAR:
                                case ReplyBtns::PERSONAL_YEARLY_HOROSCOPE_TEXT:
                                    try {
                                        if ($subIsActive) {
                                            $this->sendHoroscope(
                                                PersonalHoroscopeRep::getYearlyHoroByUserId($user->id)->text,
                                                $chat
                                            );
                                        } else {
                                            $chat
                                                ->message(Texts::NOT_SUBSCRIBER_TEXT)
                                                ->send();
                                            $chat->invoice(Subscriber::TITLE)
                                                ->description(Subscriber::DESCRIPTION)
                                                ->currency(Subscriber::CURRENCY)
                                                ->payload(Subscriber::PAYMENT_PAYLOAD)
                                                ->addItem(
                                                    Subscriber::CURRENCY,
                                                    Subscriber::PAYMENT_COST
                                                )
                                                ->send();
                                        }
                                    } catch (Exception $e) {
                                        $chat
                                            ->message($e->getMessage())
                                            ->send();
                                    }
                                    break;
                                default:
                                    if ($user->set_select_amulet_lvl_state) {
                                        switch ($messageText) {
                                            case ReplyBtns::HIGH_AMULET_LVL_TEXT:
                                            case ReplyBtns::MIDD_AMULET_LVL_TEXT:
                                            case ReplyBtns::WEAK_AMULET_LVL_TEXT:
                                                $chat
                                                    ->message('Выбери категорию оберега')
                                                    ->replyKeyboard(ReplyBtns::amuletsCategoryKeyBoard())
                                                    ->send();
                                                $user->selected_amulet_lvl = AmuletRep::AMULET_LVL[$messageText];
                                                $user->update();
                                                break;
                                            case ReplyBtns::HEALTH_CATEGORY_TEXT:
                                            case ReplyBtns::FINANCE_CATEGORY_TEXT:
                                            case ReplyBtns::RELATIONSHIP_CATEGORY_TEXT:
                                            case ReplyBtns::FAMILY_WELL_BEING_CATEGORY_TEXT:
                                                $chat
                                                    ->html(sprintf('Вот обереги %s уровня из категории <strong>%s</strong>', Texts::AMULETS[$user->selected_amulet_lvl], $messageText))
                                                    ->replyKeyboard(ReplyBtns::amuletsCategoryKeyBoard())
                                                    ->send();
                                                try {
                                                    $amulets = AmuletRep::getAmuletsByCategoryAndLevel(
                                                        AmuletRep::AMULET_TYPE[$messageText],
                                                        $user->selected_amulet_lvl
                                                    );
                                                    if ($amulets->isEmpty()) {
                                                        //throw new Exception('Ошибка при получении оберегов: коллекция пуста');
                                                        throw new Exception($amulets->count());
                                                    }
                                                    foreach ($amulets as $amulet) {
                                                        PaidMedia::sendPaidMedia(
                                                            $bot->token,
                                                            $chatId,
                                                            Storage::url(sprintf('/amulets/%s/%s/%s', $amulet->lvl, $amulet->category, $amulet->image)),
                                                            $amulet->price,
                                                            $amulet->name
                                                        );
                                                    }
                                                } catch (Exception $e) {
                                                    $chat
                                                        ->message($e->getMessage())
                                                        ->replyKeyboard(ReplyBtns::amuletsCategoryKeyBoard())
                                                        ->send();
                                                }
                                                $user->selected_amulet_type = AmuletRep::AMULET_TYPE[$messageText];
                                                $user->set_select_amulet_lvl_state = true;
                                                $user->update();
                                                break;
                                            }
                                    }
                                    break;
                            }
                            break;
                        case true:
                            switch ($messageText) {
                                case ReplyBtns::COMMAND_START:
                                    $chat
                                        ->html(Texts::greatingText($request->message['chat']['username']))
                                        ->replyKeyboard(ReplyBtns::userSettingsKeyBoard())
                                        ->send();
                                    break;
                                case ReplyBtns::SEE_PROFILE_TEXT:
                                $chat
                                    ->html("Вот твой профиль:
                                    \n<strong>Имя</strong> - {$user->name}\n<strong>Возраст</strong> - {$user->age}\n<strong>Пол</strong> - {$user->sex}\n<strong>Дата рождения</strong> - {$user->birthday}\n<strong>Знак зодиака</strong> - {$user->zodiac_horoscope}\n<strong>Животное в китайском гороскопе</strong> - {$user->chinese_horoscope}
                                    ")
                                    ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                    ->send();
                                    break;
                                case ReplyBtns::TEXT_SET_SEX:
                                    if ($user->set_info_state) {
                                        $chat
                                            ->html('Укажи пол в формате М/Ж')
                                            ->replyKeyboard(ReplyBtns::selectSexKeyBoard())
                                            ->send();
                                        $user->set_sex_state = true;
                                        $user->update();
                                    }
                                    break;
                                case ReplyBtns::TEXT_SET_BIRTHDAY:
                                    if ($user->set_info_state) {
                                        $chat
                                            ->html('Укажи дату рождения в формате дд.мм.гггг')
                                            ->replyKeyboard(ReplyBtns::userSettingsKeyBoard())
                                            ->send();
                                        $user->set_birthday_state = true;
                                        $user->update();
                                    }
                                    break;
                                case ReplyBtns::TEXT_SET_NAME:
                                    if ($user->set_info_state) {
                                        $chat
                                            ->html('Укажи имя')
                                            ->replyKeyboard(ReplyBtns::userSettingsKeyBoard())
                                            ->send();
                                        $user->set_name_state = true;
                                        $user->update();
                                    }
                                    break;
                                default:
                                    try {
                                        if ($user->set_sex_state) {
                                            if (in_array($messageText, [ReplyBtns::MALE_TEXT, ReplyBtns::FEMALE_TEXT])) {
                                                $user->sex = $messageText;
                                                $user->set_sex_state = false;
                                                $user->update();
                                            } else {
                                                throw new Exception('Неверно указан пол');
                                            }

                                        } else if ($user->set_name_state) {
                                            if (mb_strlen($messageText) < 2) {
                                                throw new Exception('Имя слишком короткое');
                                            }
                                            $user->name = $messageText;
                                            $user->set_name_state = false;
                                            $user->update();
                                        } else if ($user->set_birthday_state) {
                                            try {
                                                $user->birthday = Carbon::createFromFormat('d.m.Y', $messageText)->timestamp;
                                                $user->age = abs(intval(Carbon::now()->diffInYears(Carbon::parse($messageText))));
                                                $user->zodiac_horoscope = $this->getZodiacSign($messageText);
                                                $user->chinese_horoscope = $this->getChineseZodiacSign($messageText);
                                                $user->set_birthday_state = false;
                                                $user->update();
                                            } catch (Exception $e) {
                                                throw new Exception('Неверный формат даты');
                                            }

                                        }
                                        if ($this->checkUserSettings($user)) {
                                            $user->set_info_state = false;
                                            $user->update();
                                            $chat
                                                ->html('<strong>Отлично</strong>, теперь ты можешь пользоваться мной дальше!')
                                                ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                                ->send();
                                        } else {
                                            $chat
                                                ->html('<strong>Отлично</strong>, давай дальше!')
                                                ->replyKeyboard(ReplyBtns::userSettingsKeyBoard())
                                                ->send();
                                        }
                                    } catch (Exception $e) {
                                        $chat
                                            ->html($e->getMessage())
                                            ->replyKeyboard(ReplyBtns::userSettingsKeyBoard())
                                            ->send();
                                    }
                                    break;
                            }
                            break;
                    }
                } else if (isset($request->pre_checkout_query)) {
                    $chat = ChatRep::getChat($request->pre_checkout_query['from']['id']);
                    try{
                        PaidMedia::answerPreCheckoutQuery(
                            $bot->token,
                            $request->pre_checkout_query['id']
                        );
                    } catch (Exception $e) {
                        $chat
                            ->message($e->getMessage())
                            ->send();
                    }
                } else if (isset($request->successful_payment)) {
                    $chat = ChatRep::getChat($request->successful_payment['from']['id']);
                    $user = UserRep::getUser($chat->chat_id);
                    try {
                        if ($request->successful_payment['invoice_payload'] === Subscriber::PAYMENT_PAYLOAD) {
                            $chat
                                ->message(Texts::SUCCESSFUL_PAYMENT_TEXT)
                                ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                                ->send();
                            SubscriberRep::setSubscription($user->id);
                        }
                    } catch (Exception $e) {
                        $chat
                        ->message($e->getMessage())
                        ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                        ->send();
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function checkUserSettings(User $user): bool
    {
        return $user->sex > 0 && $user->birthday !== null && $user->name !== null;
    }

    public function sendHoroscope(string $text, TelegraphChat $chat)
    {
        $texts = explode(
            ZodiacRep::SEPARATOR,
            $text
        );
        foreach ($texts as $paragraph) {
            $chat
                ->html($paragraph)
                ->replyKeyboard(ReplyBtns::defaultKeyBoard())
                ->send();
        }
    }

    public function getZodiacSign($birthDate): string
    {
        $date = DateTime::createFromFormat('d.m.Y', $birthDate);

        // Получаем день и месяц
        $day = (int)$date->format('d');
        $month = (int)$date->format('m');

        $result = '';
        // Определяем знак зодиака
        if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) {
            $result = "Овен";
        } elseif (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
            $result = "Телец";
        } elseif (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) {
            $result = "Близнецы";
        } elseif (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) {
            $result = "Рак";
        } elseif (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) {
            $result = "Лев";
        } elseif (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
            $result = "Дева";
        } elseif (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) {
            $result = "Весы";
        } elseif (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) {
            $result = "Скорпион";
        } elseif (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) {
            $result = "Стрелец";
        } elseif (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) {
            $result = "Козерог";
        } elseif (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
            $result = "Водолей";
        } elseif (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
            $result = "Рыбы";
        }

        return $result;
    }

    public function getChineseZodiacSign($birthDate): string
    {
        $date = DateTime::createFromFormat('d.m.Y', $birthDate);

        // Получаем год рождения
        $year = (int)$date->format('Y');

        // Китайский Новый год начинается не 1 января, а в конце января/феврале.
        // Для точности нужно учитывать это, но для простоты будем считать по григорианскому году.
        // Если нужен точный расчёт с учётом Китайского Нового года, логику можно усложнить.

        // Базовый год для цикла (1900 — год Крысы)
        $baseYear = 1900;
        $cyclePosition = ($year - $baseYear) % 12;
        if ($cyclePosition < 0) {
            $cyclePosition += 12; // Для годов до 1900
        }

        // Определяем знак по позиции в 12-летнем цикле
        $zodiacSigns = [
            0 => "Крыса",
            1 => "Бык",
            2 => "Тигр",
            3 => "Кролик",
            4 => "Дракон",
            5 => "Змея",
            6 => "Лошадь",
            7 => "Коза",
            8 => "Обезьяна",
            9 => "Петух",
            10 => "Собака",
            11 => "Свинья"
        ];

        return $zodiacSigns[$cyclePosition];
    }

    public static function sendNotifiation(TelegraphChat $chat)
    {
        $chat
            ->html("❗❗❗\n" . Texts::NOTIFICATIONS[array_rand(Texts::NOTIFICATIONS)] . "\n❗❗❗")
            ->send();
    }
}
