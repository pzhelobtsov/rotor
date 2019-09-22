<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UploadTrait;
use Curl\Curl;
use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;

/**
 * Class User
 *
 * @property int id
 * @property string login
 * @property string password
 * @property string email
 * @property string level
 * @property string name
 * @property string country
 * @property string city
 * @property string language
 * @property string info
 * @property string site
 * @property string phone
 * @property string gender
 * @property string birthday
 * @property int visits
 * @property int newprivat
 * @property int newwall
 * @property int allforum
 * @property int allguest
 * @property int allcomments
 * @property string themes
 * @property string timezone
 * @property int point
 * @property int money
 * @property string status
 * @property string avatar
 * @property string picture
 * @property int rating
 * @property int posrating
 * @property int negrating
 * @property string keypasswd
 * @property int timepasswd
 * @property int sendprivatmail
 * @property int timebonus
 * @property string confirmregkey
 * @property int newchat
 * @property int notify
 * @property string apikey
 * @property string subscribe
 * @property int timeban
 * @property int updated_at
 * @property int created_at
 */
class User extends BaseModel
{
    use UploadTrait;

    public const BOSS   = 'boss';   // Владелец
    public const ADMIN  = 'admin';  // Админ
    public const MODER  = 'moder';  // Модератор
    public const EDITOR = 'editor'; // Редактор
    public const USER   = 'user';   // Пользователь
    public const PENDED = 'pended'; // Ожидающий
    public const BANNED = 'banned'; // Забаненный

    /**
     * Администраторы
     */
    public const ADMIN_GROUPS = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
    ];

    /**
     * Участники
     */
    public const USER_GROUPS = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
        self::USER,
    ];

    /**
     * Все пользователи
     */
    public const ALL_GROUPS = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
        self::USER,
        self::PENDED,
        self::BANNED,
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Директория загрузки файлов
     *
     * @var string
     */
    public $uploadPath = UPLOADS . '/pictures';

    /**
     * Директория загрузки аватаров
     *
     * @var string
     */
    public $uploadAvatarPath = UPLOADS . '/avatars';

    /**
     * Связь с таблицей online
     *
     * @return BelongsTo
     */
    public function online(): BelongsTo
    {
        return $this->belongsTo(Online::class, 'id', 'user_id')->withDefault();
    }

    /**
     * Возвращает последний бан
     *
     * @return hasOne
     */
    public function lastBan(): hasOne
    {
        return $this->hasOne(Banhist::class, 'user_id', 'id')
            ->whereIn('type', ['ban', 'change'])
            ->orderBy('created_at', 'desc')
            ->withDefault();
    }

    /**
     * Возвращает заметку пользователя
     *
     * @return hasOne
     */
    public function note(): HasOne
    {
        return $this->hasOne(Note::class)->withDefault();
    }

    /**
     * Возвращает имя или логин пользователя
     *
     * @return string
     */
    public function getName(): string
    {
        if ($this->id) {
            return $this->name ?: $this->login;
        }

        return setting('deleted_user');
    }

    /**
     * Возвращает ссылку на профиль пользователя
     *
     * @param  string $color цвет логина
     * @return string        путь к профилю
     */
    public function getProfile($color = null): string
    {
        if ($this->id) {
            $admin = null;
            $name  = $this->getName();

            if ($color) {
                $name = '<span style="color:' . $color . '">' . $name . '</span>';
            }

            if (in_array($this->level, self::ADMIN_GROUPS, true)) {
                $admin = ' <i class="fas fa-xs fa-star text-info"></i>';
            }

            return '<a class="author" href="/users/' . $this->login . '" data-login="' . $this->login . '">' . $name . '</a>' . $admin;
        }

        return '<span class="author" data-login="' . setting('deleted_user') . '">' . setting('deleted_user') . '</span>';
    }

    /**
     * Возвращает пол пользователя
     *
     * @return string пол пользователя
     */
    public function getGender(): string
    {
        if ($this->gender === 'female') {
            return '<i class="fa fa-female fa-lg"></i>';
        }

        return '<i class="fa fa-male fa-lg"></i>';
    }

    /**
     * Авторизует пользователя
     *
     * @param  string  $login    Логин
     * @param  string  $password Пароль пользователя
     * @param  boolean $remember Запомнить пароль
     * @return Builder|Model|bool
     */
    public static function auth($login, $password, $remember = true)
    {
        $domain = siteDomain(siteUrl());

        if (! empty($login) && ! empty($password)) {

            $user = getUserByLogin($login);

            if ($user && password_verify($password, $user['password'])) {

                if ($remember) {
                    setcookie('login', $user->login, strtotime('+1 year', SITETIME), '/', $domain);
                    setcookie('password', md5($user->password . env('APP_KEY')), strtotime('+1 year', SITETIME), '/', $domain, false, true);
                }

                $_SESSION['id']       = $user->id;
                $_SESSION['password'] = md5(env('APP_KEY') . $user->password);

                // Сохранение привязки к соц. сетям
                if (! empty($_SESSION['social'])) {
                    Social::query()->create([
                        'user_id'    => $user->id,
                        'network'    => $_SESSION['social']->network,
                        'uid'        => $_SESSION['social']->uid,
                        'created_at' => SITETIME,
                    ]);
                }

                return self::saveVisit($user, 1);
            }
        }

        return false;
    }

    /**
     * Сохраняет посещения
     *
     * @param User $user
     * @param int  $type
     * @return User
     */
    public static function saveVisit(User $user, int $type = 0): User
    {
        $authorization = Login::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>', SITETIME - 60)
            ->first();

        if (! $authorization) {
            Login::query()->create([
                'user_id'    => $user->id,
                'ip'         => getIp(),
                'brow'       => getBrowser(),
                'created_at' => SITETIME,
                'type'       => $type,
            ]);
        }

        $user->increment('visits');
        $user->update(['updated_at' => SITETIME]);

        return $user;
    }

    /**
     * Авторизует через социальные сети
     *
     * @param string $token идентификатор Ulogin
     * @return void
     * @throws ErrorException
     */
    public static function socialAuth($token): void
    {
        $domain = siteDomain(siteUrl());

        $curl = new Curl();
        $network = $curl->get('//ulogin.ru/token.php',
            [
                'token' => $token,
                'host'  => $_SERVER['HTTP_HOST']
            ]
        );

        if ($network && empty($network->error)) {
            $_SESSION['social'] = $network;

            $social = Social::query()
                ->where('network', $network->network)
                ->where('uid', $network->uid)
                ->first();

            if ($social && $user = getUserById($social->user_id)) {

                setcookie('login', $user->login, strtotime('+1 year', SITETIME), '/', $domain);
                setcookie('password', md5($user->password . env('APP_KEY')), strtotime('+1 year', SITETIME), '/', $domain, false, true);

                $_SESSION['id']       = $user->id;
                $_SESSION['password'] = md5(env('APP_KEY') . $user->password);

                setFlash('success', 'Добро пожаловать, ' . $user->login . '!');
                redirect('/');
            }
        }
    }

    /**
     * Возвращает название уровня по ключу
     *
     * @param  string $level
     * @return string
     */
    public static function getLevelByKey(string $level): string
    {
        switch ($level) {
            case self::BOSS:
                $status = __('main.boss');
                break;
            case self::ADMIN:
                $status = __('main.admin');
                break;
            case self::MODER:
                $status = __('main.moder');
                break;
            case self::EDITOR:
                $status = __('main.editor');
                break;
            case self::USER:
                $status = __('main.user');
                break;
            case self::PENDED:
                $status = __('main.pended');
                break;
            case self::BANNED:
                $status = __('main.banned');
                break;
            default: $status = setting('statusdef');
        }

        return $status;
    }

    /**
     * Возвращает уровень пользователя
     *
     * @return string Уровень пользователя
     */
    public function getLevel(): string
    {
        return self::getLevelByKey($this->level);
    }

    /**
     * Возвращает онлайн-статус пользователя
     *
     * @return string онлайн-статус
     */
    public function getOnline(): string
    {
        static $visits;

        $online = '<div class="online bg-danger" title="Оффлайн"></div>';

        if (! $visits) {
            if (@filemtime(STORAGE . '/temp/visit.dat') < time() - 10) {

                $onlines = Online::query()
                    ->whereNotNull('user_id')
                    ->pluck('user_id', 'user_id')
                    ->all();

                file_put_contents(STORAGE . '/temp/visit.dat', json_encode($onlines), LOCK_EX);
            }

            $visits = json_decode(file_get_contents(STORAGE . '/temp/visit.dat'));
        }

        if (isset($visits->{$this->id})) {
            $online = '<div class="online bg-success" title="Онлайн"></div>';
        }

        return $online;
    }

    /**
     * Возвращает статус пользователя
     *
     * @return string статус пользователя
     */
    public function getStatus(): string
    {
        static $status;

        if (! $this->id) {
            return setting('statusdef');
        }

        if (! $status) {
            $this->saveStatus(3600);
            $status = json_decode(file_get_contents(STORAGE . '/temp/status.dat'));
        }

        return $status->{$this->id} ?? setting('statusdef');
    }

    /**
     * Возвращает аватар для пользователя по умолчанию
     *
     * @return string код аватара
     */
    public function defaultAvatar(): string
    {
        $name   = $this->name ?: $this->login;
        $color  = '#' . substr(dechex(crc32($this->login)), 0, 6);
        $letter = mb_strtoupper(utfSubstr($name, 0, 1), 'utf-8');

        return '<div class="avatar" style="background:' . $color . '"><a href="/users/' . $this->login . '">' . $letter . '</a></div>';
    }

    /**
     * Возвращает аватар пользователя
     *
     * @return string аватар пользователя
     */
    public function getAvatar(): string
    {
        if (! $this->id) {
            return '<img class="avatar" src="/assets/img/images/avatar_default.png" alt=""> ';
        }

        if ($this->avatar && file_exists(HOME . '/' . $this->avatar)) {
            return '<a class="avatar" href="/users/' . $this->login . '"><img class="avatar" src="' . $this->avatar . '" alt=""></a> ';
        }

        return $this->defaultAvatar();
    }

    /**
     * Временный метод
     *
     * @return string
     */
    public function getAvatarImage(): string
    {
        if ($this->avatar && file_exists(HOME . '/' . $this->avatar)) {
            return '<img src="' . $this->avatar . '" alt="" class="avatar-image">';
        }

        return '<img class="avatar-image" src="/assets/img/images/avatar_guest.png" alt="">';
    }

    /**
     * Кеширует статусы пользователей
     *
     * @param  int $time время кеширования
     * @return void
     */
    public function saveStatus($time = 0): void
    {
        if (empty($time) || @filemtime(STORAGE . '/temp/status.dat') < time() - $time) {

            $users = self::query()
                ->select('users.id', 'users.status', 'status.name', 'status.color')
                ->leftJoin('status', static function (JoinClause $join) {
                    $join->whereRaw('users.point between status.topoint and status.point');
                })
                ->where('users.point', '>', 0)
                ->get();

            $statuses = [];
            foreach ($users as $user) {

                if ($user->status) {
                    $statuses[$user->id] = '<span style="color:#ff0000">' . $user->status . '</span>';
                    continue;
                }

                if ($user->color) {
                    $statuses[$user->id] = '<span style="color:' . $user->color . '">' . $user->name . '</span>';
                    continue;
                }

                $statuses[$user->id] = $user->name;
            }

            file_put_contents(STORAGE . '/temp/status.dat', json_encode($statuses, JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
    }

    /**
     * Возвращает находится ли пользователь в контакатх
     *
     * @param  User $user объект пользователя
     * @return bool       находится ли в контактах
     */
    public function isContact(User $user): bool
    {
        $isContact = Contact::query()
            ->where('user_id', $this->id)
            ->where('contact_id', $user->id)
            ->first();

        if ($isContact) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает находится ли пользователь в игноре
     *
     * @param  User $user объект пользователя
     * @return bool       находится ли в игноре
     */
    public function isIgnore(User $user): bool
    {
        $isIgnore = Ignore::query()
            ->where('user_id', $this->id)
            ->where('ignore_id', $user->id)
            ->first();

        if ($isIgnore) {
            return true;
        }

        return false;
    }

    /**
     * Отправляет приватное сообщение
     *
     * @param  User|null $author Отправитель
     * @param  int       $text   текст сообщения
     * @return bool              результат отправки
     */
    public function sendMessage(?User $author, $text): bool
    {
        Message::query()->create([
            'user_id'    => $this->id,
            'author_id'  => $author ? $author->id : 0,
            'text'       => $text,
            'created_at' => SITETIME,
        ]);

        $this->increment('newprivat');

        return true;
    }

    /**
     * Возвращает количество писем пользователя
     *
     * @return int количество писем
     */
    public function getCountMessages(): int
    {
        return Message::query()->where('user_id', $this->id)->count();
    }

    /**
     * Возвращает размер контакт-листа
     *
     * @return int количество контактов
     */
    public function getCountContact(): int
    {
        return Contact::query()->where('user_id', $this->id)->count();
    }

    /**
     * Возвращает размер игнор-листа
     *
     * @return int количество игнорируемых
     */
    public function getCountIgnore(): int
    {
        return Ignore::query()->where('user_id', $this->id)->count();
    }

    /**
     * Возвращает количество записей на стене сообщений
     *
     * @return int количество записей
     */
    public function getCountWall(): int
    {
        return Wall::query()->where('user_id', $this->id)->count();
    }

    /**
     * Удаляет альбом пользователя
     *
     * @return void
     */
    public function deleteAlbum(): void
    {
        $photos = Photo::query()->where('user_id', $this->id)->get();

        if ($photos->isNotEmpty()) {
            foreach ($photos as $photo) {
                $photo->comments()->delete();
                $photo->delete();
            }
        }
    }

    /**
     * Удаляет записи пользователя из всех таблиц
     *
     * @return bool|null  результат удаления
     * @throws Exception
     */
    public function delete(): ?bool
    {
        deleteFile(HOME . $this->picture);
        deleteFile(HOME . $this->avatar);

        Message::query()->where('user_id', $this->id)->delete();
        Contact::query()->where('user_id', $this->id)->delete();
        Ignore::query()->where('user_id', $this->id)->delete();
        Rating::query()->where('user_id', $this->id)->delete();
        Wall::query()->where('user_id', $this->id)->delete();
        Note::query()->where('user_id', $this->id)->delete();
        Notebook::query()->where('user_id', $this->id)->delete();
        Banhist::query()->where('user_id', $this->id)->delete();
        Bookmark::query()->where('user_id', $this->id)->delete();
        Login::query()->where('user_id', $this->id)->delete();
        Invite::query()->where('user_id', $this->id)->orWhere('invite_user_id', $this->id)->delete();

        return parent::delete();
    }
}
