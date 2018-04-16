<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Classes\Validator;
use App\Models\Down;
use App\Models\File;
use App\Models\Load;
use App\Models\User;

class LoadController extends AdminController
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();

        if (! isAdmin(User::ADMIN)) {
            abort(403, 'Доступ запрещен!');
        }
    }

    /**
     * Главная страница
     */
    public function index()
    {
        $categories = Load::query()
            ->where('parent_id', 0)
            ->with('children', 'new', 'children.new')
            ->orderBy('sort')
            ->get();

        return view('admin/load/index', compact('categories'));
    }

    /**
     * Создание раздела
     */
    public function create()
    {
        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        $token = check(Request::input('token'));
        $name  = check(Request::input('name'));

        $validator = new Validator();
        $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
            ->length($name, 5, 50, ['title' => 'Слишком длинное или короткое название раздела!']);

        if ($validator->isValid()) {

            $max = Load::query()->max('sort') + 1;

            $load = Load::query()->create([
                'name' => $name,
                'sort' => $max,
            ]);

            setFlash('success', 'Новый раздел успешно создан!');
            redirect('/admin/load/edit/' . $load->id);
        } else {
            setInput(Request::all());
            setFlash('danger', $validator->getErrors());
        }

        redirect('/admin/load');
    }

    /**
     * Редактирование раздела
     */
    public function edit($id)
    {
        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        $load = Load::query()->with('children')->find($id);

        if (! $load) {
            abort(404, 'Данного раздела не существует!');
        }

        $loads = Load::query()
            ->where('parent_id', 0)
            ->orderBy('sort')
            ->get();

        if (Request::isMethod('post')) {
            $token  = check(Request::input('token'));
            $parent = int(Request::input('parent'));
            $name   = check(Request::input('name'));
            $sort   = check(Request::input('sort'));
            $closed = empty(Request::input('closed')) ? 0 : 1;

            $validator = new Validator();
            $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
                ->length($name, 5, 50, ['title' => 'Слишком длинное или короткое название раздела!'])
                ->notEqual($parent, $load->id, ['parent' => 'Недопустимый выбор родительского раздела!']);

            if (! empty($parent) && $load->children->isNotEmpty()) {
                $validator->addError(['parent' => 'Текущий раздел имеет подразделы!']);
            }

            if ($validator->isValid()) {

                $load->update([
                    'parent_id' => $parent,
                    'name'      => $name,
                    'sort'      => $sort,
                    'closed'    => $closed,
                ]);

                setFlash('success', 'Раздел успешно отредактирован!');
                redirect('/admin/load');
            } else {
                setInput(Request::all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('admin/load/edit', compact('loads', 'load'));
    }

    /**
     * Удаление раздела
     */
    public function delete($id)
    {
        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        $load = Load::query()->with('children')->find($id);

        if (! $load) {
            abort(404, 'Данного раздела не существует!');
        }

        $token = check(Request::input('token'));

        $validator = new Validator();
        $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
            ->true($load->children->isEmpty(), 'Удаление невозможно! Данный раздел имеет подразделы!');

        $down = Down::query()->where('category_id', $load->id)->first();
        if ($down) {
            $validator->addError('Удаление невозможно! В данном разделе имеются загрузки!');
        }

        if ($validator->isValid()) {

            $load->delete();

            setFlash('success', 'Раздел успешно удален!');
        } else {
            setFlash('danger', $validator->getErrors());
        }

        redirect('/admin/load');
    }

    /**
     * Пересчет данных
     */
    public function restatement()
    {
        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        $token = check(Request::input('token'));

        if ($token == $_SESSION['token']) {

            restatement('load');

            setFlash('success', 'Данные успешно пересчитаны!');
        } else {
            setFlash('danger', 'Ошибка! Неверный идентификатор сессии, повторите действие!');
        }

        redirect('/admin/load');
    }

    /**
     * Просмотр загрузок раздела
     */
    public function load($id)
    {
        $category = Load::query()->with('parent')->find($id);

        if (! $category) {
            abort(404, 'Данной категории не существует!');
        }

        $total = Down::query()->where('category_id', $category->id)->where('active', 1)->count();
        $page = paginate(setting('downlist'), $total);

        $sort = check(Request::input('sort'));

        switch ($sort) {
            case 'rated':
                $order = 'rated';
                break;
            case 'comments':
                $order = 'count_comments';
                break;
            case 'loads':
                $order = 'loads';
                break;
            default:
                $order = 'created_at';
        }

        $downs = Down::query()
            ->where('category_id', $category->id)
            ->where('active', 1)
            ->orderBy($order, 'desc')
            ->offset($page->offset)
            ->limit($page->limit)
            ->get();

        return view('admin/load/load', compact('category', 'downs', 'page', 'order'));
    }

    /**
     * Редактирование загрузки
     */
    public function editDown($id)
    {
        $down = Down::query()->find($id);

        if (! $down) {
            abort(404, 'Данного файла не существует!');
        }

        if (Request::isMethod('post')) {
            $token    = check(Request::input('token'));
            $category = check(Request::input('category'));
            $title    = check(Request::input('title'));
            $text     = check(Request::input('text'));
            $files    = (array) Request::file('files');

            $category = Load::query()->find($category);

            $validator = new Validator();
            $validator->equal($token, $_SESSION['token'], 'Неверный идентификатор сессии, повторите действие!')
                ->length($title, 5, 50, ['title' => 'Слишком длинное или короткое название!'])
                ->length($text, 50, 5000, ['text' => 'Слишком длинное или короткое описание!'])
                ->notEmpty($category, ['category' => 'Категории для данного файла не существует!']);

            $duplicate = Down::query()->where('title', $title)->where('id', '<>', $down->id)->count();
            $validator->empty($duplicate, ['title' => 'Загрузка с аналогичный названием уже существует!']);

            $existFiles = $down->files ? $down->files->count() : 0;
            $validator->lte(count($files) + $existFiles, 5, ['files' => 'Разрешено загружать не более 5 файлов']);

            if ($validator->isValid()) {

                $rules = [
                    'maxsize'    => setting('fileupload'),
                    'extensions' => explode(',', setting('allowextload')),
                    'maxweight'  => setting('screenupsize'),
                    'minweight'  => 100,
                ];

                foreach ($files as $file) {
                    $validator->file($file, $rules, ['files' => 'Не удалось загрузить файл!']);
                }
            }

            if ($validator->isValid()) {

                $oldDown = $down->replicate();

                $down->update([
                    'category_id' => $category->id,
                    'title'       => $title,
                    'text'        => $text,
                ]);

                if ($down->category->id != $oldDown->category->id && $down->active) {
                    $down->category->increment('count_downs');
                    $oldDown->category->decrement('count_downs');
                }

                foreach ($files as $file) {
                    $down->uploadFile($file);
                }

                setFlash('success', 'Загрузка успешно отредактирована!');
                redirect('/admin/down/edit/' . $down->id);
            } else {
                setInput(Request::all());
                setFlash('danger', $validator->getErrors());
            }
        }

        $categories = Load::query()
            ->where('parent_id', 0)
            ->with('children', 'new', 'children.new')
            ->orderBy('sort')
            ->get();

        return view('admin/load/edit_down', compact('categories', 'down'));
    }

    /**
     * Удаление файла
     */
    public function deleteFile($id, $fid)
    {
        $down = Down::query()->find($id);

        if (! $down) {
            abort(404, 'Файла не существует!');
        }

        $file = File::query()->where('relate_id', $down->id)->find($fid);

        if (! $file) {
            abort('default', 'Файла не существует!');
        }

        if ($file->isImage()) {
            deleteFile(UPLOADS . '/screen/' . $file->hash);
        } else {
            deleteFile(UPLOADS . '/files/' . $file->hash);
        }

        setFlash('success', 'Файл успешно удален!');
        $file->delete();

        redirect('/admin/down/edit/' . $down->id);
    }
}
