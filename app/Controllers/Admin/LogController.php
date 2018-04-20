<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Classes\Validator;
use App\Models\Log;
use App\Models\User;

class LogController extends AdminController
{
    /**
     * @var array
     */
    private $lists;

    /**
     * @var int
     */
    private $code;

    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();

        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        $this->code  = int(Request::input('code', 404));
        $this->lists = [404 => 'Ошибки 404', 403 => 'Ошибки 403', 666 => 'Автобаны'];

        if (! isset($this->lists[$this->code])) {
            abort(404, 'Указанный лог-файл не существует!');
        }
    }

    /**
     * Главная страница
     */
    public function index()
    {
        $lists = $this->lists;
        $code  = $this->code;

        $total = Log::query()->where('code', $code)->count();
        $page = paginate(setting('loglist'), $total);

        $logs = Log::query()
            ->where('code', $code)
            ->orderBy('created_at', 'desc')
            ->offset($page->offset)
            ->limit($page->limit)
            ->with('user')
            ->get();

        return view('admin/log/index', compact('logs', 'page', 'code', 'lists'));
    }

    /**
     * Очистка логов
     */
    public function clear()
    {
        $token = check(Request::input('token'));

        $validator = new Validator();
        $validator
            ->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
            ->true(isAdmin(User::BOSS), 'Очищать логи может только владелец!');

        if ($validator->isValid()) {

            Log::query()->truncate();

            setFlash('success', 'Логи успешно очищены!');
        } else {
            setFlash('danger', $validator->getErrors());
        }

        redirect('/admin/log');
    }
}
