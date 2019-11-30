<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MailController extends BaseController
{
    /**
     * Главная страница
     *
     * @param Request   $request
     * @param Validator $validator
     * @return string
     */
    public function index(Request $request, Validator $validator): string
    {
        if ($request->isMethod('post')) {
            $message = nl2br(check($request->input('message')));
            $name    = check($request->input('name'));
            $email   = check($request->input('email'));

            if (getUser()) {
                $name  = getUser('login');
                $email = getUser('email');
            }

            $validator->true(captchaVerify(), ['protect' => __('validator.captcha')])
                ->length($name, 3, 100, ['name' => __('mails.name_short_or_long')])
                ->length($message, 5, 50000, ['message' => __('validator.text')])
                ->email($email, ['email' => __('validator.email')]);

            if ($validator->isValid()) {
                $message .= '<br><br>IP: ' . getIp() . '<br>Браузер: ' . getBrowser() . '<br>Отправлено: ' . dateFixed(SITETIME, 'j.m.Y / H:i');

                $subject = 'Письмо с сайта ' . setting('title');
                $body = view('mailer.default', compact('subject', 'message'));
                sendMail(config('SITE_EMAIL'), $subject, $body, ['from' => [$email => $name]]);

                setFlash('success', __('mails.success_sent'));
                redirect('/');
            } else {
                setInput($request->all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('mails/index');
    }

    /**
     * Восстановление пароля
     *
     * @param Request   $request
     * @param Validator $validator
     * @return string
     */
    public function recovery(Request $request, Validator $validator): string
    {
        if (getUser()) {
            abort('default', __('main.already_authorized'));
        }

        $cookieLogin = isset($_COOKIE['login']) ? check($_COOKIE['login']) : '';

        if ($request->isMethod('post')) {
            $login = check($request->input('user'));

            $user = getUserByLoginOrEmail($login);
            if (! $user) {
                abort('default', __('validator.user'));
            }

            $validator->true(captchaVerify(), ['protect' => __('validator.captcha')])
                ->lte($user['timepasswd'], SITETIME, ['user' => __('mails.password_recovery_time')]);

            if ($validator->isValid()) {
                $resetKey  = Str::random();
                $resetLink = siteUrl(true) . '/restore?key=' . $resetKey;

                $user->update([
                    'keypasswd'  => $resetKey,
                    'timepasswd' => strtotime('+1 hour', SITETIME),
                ]);

                //Инструкция по восстановлению пароля на email
                $subject = 'Восстановление пароля на сайте ' . setting('title');
                $message = 'Здравствуйте, ' . $user['login'] . '<br>Вами была произведена операция по восстановлению пароля на сайте <a href="' . siteUrl(true) . '">' . setting('title') . '</a><br><br>Данные отправителя:<br>Ip: ' . getIp() . '<br>Браузер: ' . getBrowser() . '<br>Отправлено: ' . date('j.m.Y / H:i', SITETIME) . '<br><br>Для того чтобы восстановить пароль, вам необходимо нажать на кнопку восстановления<br>Если это письмо попало к вам по ошибке или вы не собираетесь восстанавливать пароль, то просто проигнорируйте его';

                $body = view('mailer.recovery', compact('subject', 'message', 'resetLink'));
                sendMail($user['email'], $subject, $body);

                setFlash('success', __('mails.recovery_instructions', ['email' => hideMail($user['email'])]));
                redirect('/login');
            } else {
                setInput($request->all());
                setFlash('danger', $validator->getErrors());
            }
        }

        return view('mails/recovery', compact('cookieLogin'));
    }

    /**
     * Восстановление пароля
     *
     * @param Request   $request
     * @param Validator $validator
     * @return string
     */
    public function restore(Request $request, Validator $validator): ?string
    {
        if (getUser()) {
            abort(403, __('main.already_authorized'));
        }

        $key = check($request->input('key'));

        $user = User::query()->where('keypasswd', $key)->first();
        if (! $user) {
            abort('default', __('mails.secret_key_invalid'));
        }

        $validator->notEmpty($key, __('mails.secret_key_missing'))
            ->notEmpty($user['keypasswd'], __('mails.password_not_recovery'))
            ->gte($user['timepasswd'], SITETIME, __('mails.secret_key_expired'));

        if ($validator->isValid()) {
            $newpass    = Str::random();
            $hashnewpas = password_hash($newpass, PASSWORD_BCRYPT);

            $user->update([
                'password'   => $hashnewpas,
                'keypasswd'  => null,
                'timepasswd' => 0,
            ]);

            // Восстановление пароля на email
            $subject = 'Восстановление пароля на сайте ' . setting('title');
            $message = 'Здравствуйте, ' . $user['login'] . '<br>Ваши новые данные для входа на на сайт <a href="' . siteUrl(true) . '">' . setting('title') . '</a><br><b>Логин: ' . $user['login'] . '</b><br><b>Пароль: ' . $newpass . '</b><br><br>Запомните и постарайтесь больше не забывать данные <br>Пароль вы сможете поменять в своем профиле<br>Всего наилучшего!';

            $body = view('mailer.default', compact('subject', 'message'));
            sendMail($user['email'], $subject, $body);

            return view('mails/restore', ['login' => $user['login'], 'password' => $newpass]);
        }

        setFlash('danger', current($validator->getErrors()));
        redirect('/');
    }

    /**
     * Отписка от рассылки
     *
     * @param Request $request
     */
    public function unsubscribe(Request $request): void
    {
        $key = check($request->input('key'));

        if (! $key) {
            abort('default', __('mails.secret_key_missing'));
        }

        $user = User::query()->where('subscribe', $key)->first();

        if (! $user) {
            abort('default', __('mails.secret_key_expired'));
        }

        $user->subscribe = null;
        $user->save();

        setFlash('success', __('mails.success_unsubscribed'));
        redirect('/');
    }
}
