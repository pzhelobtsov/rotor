<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Classes\Validator;
use App\Models\Banhist;
use App\Models\User;
use Illuminate\Http\Request;

class BanController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (! isAdmin(User::MODER)) {
            abort(403, trans('errors.forbidden'));
        }
    }

    /**
     * Главная страница
     *
     * @return string
     */
    public function index(): string
    {
        return view('admin/bans/index');
    }

    /**
     * Бан пользователя
     *
     * @param Request   $request
     * @param Validator $validator
     * @return string
     */
    public function edit(Request $request, Validator $validator): string
    {
        $login = check($request->input('user'));

        $user = User::query()->where('login', $login)->with('lastBan')->first();

        if (! $user) {
            abort(404, trans('validator.user'));
        }

        if (in_array($user->level, User::ADMIN_GROUPS, true)) {
            abort('default', trans('admin.bans.forbidden_ban'));
        }

        if ($request->isMethod('post')) {
            $token  = check($request->input('token'));
            $time   = int($request->input('time'));
            $type   = check($request->input('type'));
            $reason = check($request->input('reason'));
            $notice = check($request->input('notice'));

            $validator->equal($token, $_SESSION['token'], trans('validator.token'))
                ->false($user->level === User::BANNED && $user->timeban > SITETIME, trans('admin.bans.user_banned'))
                ->gt($time, 0, ['time' => trans('admin.bans.time_not_indicated')])
                ->in($type, ['minutes', 'hours', 'days'], ['type' => trans('admin.bans.time_not_selected')])
                ->length($reason, 5, 1000, ['reason' => trans('validator.text')])
                ->length($notice, 0, 1000, ['notice' => trans('validator.text_long')]);

            if ($validator->isValid()) {

                if ($type === 'days') {
                    $time *= 86400;
                } elseif ($type === 'hours') {
                    $time *= 3600;
                } else {
                    $time *= 60;
                }

                $user->update([
                    'level'   => User::BANNED,
                    'timeban' => SITETIME + $time,
                ]);

                Banhist::query()->create([
                    'user_id'      => $user->id,
                    'send_user_id' => getUser('id'),
                    'type'         => Banhist::BAN,
                    'reason'       => $reason,
                    'term'         => $time,
                    'created_at'   => SITETIME,
                ]);

                $user->note()->updateOrCreate([], [
                    'text'         => $notice,
                    'edit_user_id' => getUser('id'),
                    'updated_at'   => SITETIME,
                ]);

                setFlash('success', trans('admin.bans.success_banned'));
                redirect('/admin/bans/edit?user=' . $user->login);
            } else {
                setInput($request->all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('admin/bans/edit', compact('user'));
    }

    /**
     * Изменение бана
     *
     * @param Request   $request
     * @param Validator $validator
     * @return string
     */
    public function change(Request $request, Validator $validator): string
    {
        $login = check($request->input('user'));

        $user = User::query()->where('login', $login)->with('lastBan')->first();

        if (! $user) {
            abort(404, trans('validator.user'));
        }

        if ($user->level !== User::BANNED || $user->timeban < SITETIME) {
            abort('default', trans('admin.bans.user_not_banned'));
        }

        if ($request->isMethod('post')) {
            $token   = check($request->input('token'));
            $timeban = check($request->input('timeban'));
            $reason  = check($request->input('reason'));

            $timeban = strtotime($timeban);
            $term    = $timeban - SITETIME;

            $validator->equal($token, $_SESSION['token'], trans('validator.token'))
                ->gt($term, 0, ['timeban' => trans('admin.bans.time_empty')])
                ->length($reason, 5, 1000, ['reason' => trans('validator.text')]);

            if ($validator->isValid()) {

                $user->update([
                    'level'   => User::BANNED,
                    'timeban' => $timeban,
                ]);

                Banhist::query()->create([
                    'user_id'      => $user->id,
                    'send_user_id' => getUser('id'),
                    'type'         => Banhist::CHANGE,
                    'reason'       => $reason,
                    'term'         => $term,
                    'created_at'   => SITETIME,
                ]);

                setFlash('success', trans('main.record_changed_success'));
                redirect('/admin/bans/edit?user=' . $user->login);
            } else {
                setInput($request->all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('admin/bans/change', compact('user'));
    }

    /**
     * Снятие бана
     *
     * @param Request   $request
     * @param Validator $validator
     * @return void
     */
    public function unban(Request $request, Validator $validator): void
    {
        $token = check($request->input('token'));
        $login = check($request->input('user'));

        $user = User::query()->where('login', $login)->with('lastBan')->first();

        if (! $user) {
            abort(404, trans('validator.user'));
        }

        if ($user->level !== User::BANNED || $user->timeban < SITETIME) {
            abort('default', trans('admin.bans.user_not_banned'));
        }

        $validator->equal($token, $_SESSION['token'], trans('validator.token'));

        if ($validator->isValid()) {

            $user->update([
                'level'   => User::USER,
                'timeban' => null,
            ]);

            Banhist::query()->create([
                'user_id'      => $user->id,
                'send_user_id' => getUser('id'),
                'type'         => Banhist::UNBAN,
                'created_at'   => SITETIME,
            ]);

            setFlash('success', trans('admin.bans.success_unbanned'));
        } else {
            setFlash('danger', $validator->getErrors());
        }

        redirect('/admin/bans/edit?user=' . $user->login);
    }
}
