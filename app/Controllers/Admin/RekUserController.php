<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Classes\Validator;
use App\Models\RekUser;
use App\Models\User;

class RekUserController extends AdminController
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();

        if (! isAdmin(User::EDITOR)) {
            abort(403, 'Доступ запрещен!');
        }
    }
    /**
     * Главная страница
     *
     * @return string
     */
    public function index(): string
    {
        $total = RekUser::query()->where('deleted_at', '>', SITETIME)->count();
        $page = paginate(setting('rekuserpost'), $total);

        $records = RekUser::query()
            ->where('deleted_at', '>', SITETIME)
            ->limit($page->limit)
            ->offset($page->offset)
            ->orderBy('deleted_at', 'desc')
            ->with('user')
            ->get();

        return view('admin/rekusers/index', compact('records', 'page'));
    }
    /**
     * Редактирование ссылки
     *
     * @param int $id
     * @return string
     */
    public function edit($id): string
    {
        $page = int(Request::input('page', 1));
        $link = RekUser::query()->find($id);

        if (! $link) {
            abort(404, 'Данной ссылки не существует!');
        }

        if (Request::isMethod('post')) {
            $token = check(Request::input('token'));
            $site  = check(Request::input('site'));
            $name  = check(Request::input('name'));
            $color = check(Request::input('color'));
            $bold  = empty(Request::input('bold')) ? 0 : 1;

            $validator = new Validator();
            $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
                ->regex($site, '|^https?://([а-яa-z0-9_\-\.])+(\.([а-яa-z0-9\/\-?_=#])+)+$|iu', ['site' => 'Недопустимый адрес сайта!. Разрешены символы [а-яa-z0-9_-.?=#/]!'])
                ->length($site, 5, 50, ['site' => 'Слишком длинный или короткий адрес ссылки!'])
                ->length($name, 5, 35, ['name' => 'Слишком длинное или короткое название ссылки!'])
                ->regex($color, '|^#+[A-f0-9]{6}$|', ['color' => 'Недопустимый формат цвета ссылки! (пример #ff0000)'], false);

            if ($validator->isValid()) {

                $link->update([
                    'site'  => $site,
                    'name'  => $name,
                    'color' => $color,
                    'bold'  => $bold,
                ]);

                saveAdvertUser();

                setFlash('success', 'Рекламная ссылка успешно изменена!');
                redirect('/admin/reklama?page=' . $page);
            } else {
                setInput(Request::all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('admin/rekusers/edit', compact('link', 'page'));
    }
    /**
     * Удаление записей
     *
     * @return void
     */
    public function delete(): void
    {
        $page  = int(Request::input('page', 1));
        $token = check(Request::input('token'));
        $del   = intar(Request::input('del'));

        $validator = new Validator();

        $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
            ->true($del, 'Отсутствуют выбранные записи для удаления!');

        if ($validator->isValid()) {

            RekUser::query()->whereIn('id', $del)->delete();

            saveAdvertUser();

            setFlash('success', 'Выбранные записи успешно удалены!');
        } else {
            setFlash('danger', $validator->getErrors());
        }

        redirect('/admin/reklama?page=' . $page);
    }
}
