<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Models\User;

class CacheController extends AdminController
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();

        if (! isAdmin(User::BOSS)) {
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
        $type = check(Request::input('type', 'files'));

        if ($type === 'files') {
            $files = glob(STORAGE . '/temp/*.dat');

            $view = view('admin/caches/index', compact('files'));
        } else {
            $images = glob(UPLOADS.'/thumbnails/*.{gif,png,jpg,jpeg}', GLOB_BRACE);
            $page   = paginate(20, \count($images));

            $images = \array_slice($images, $page->offset, $page->limit);

            $view = view('admin/caches/images', compact('images', 'page'));
        }

        return $view;
    }

    /**
     * Очистка кеша
     *
     * @return void
     */
    public function clear(): void
    {
        $token = check(Request::input('token'));
        $type  = check(Request::input('type', 'files'));

        if ($token === $_SESSION['token']) {

            if ($type === 'files') {
                clearCache();
            } else {
                $images = glob(UPLOADS.'/thumbnails/*.{gif,png,jpg,jpeg}', GLOB_BRACE);

                if ($images){
                    foreach ($images as $image) {
                        unlink ($image);
                    }
                }
            }

            setFlash('success', 'Кеш успешно очищен!');
        } else {
            setFlash('danger', 'Ошибка! Неверный идентификатор сессии, повторите действие!');
        }

        redirect('/admin/caches?type=' . $type);
    }
}
