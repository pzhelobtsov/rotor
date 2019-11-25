<?php

return [
    'antimat' => [
        'text' => '
        Все слова из списка будут заменяться на ***<br>
        Чтобы удалить слово нажмите на него, добавить слово можно в форме ниже<br>',
        'words'         => 'Список слов',
        'total_words'   => 'Всего слов',
        'confirm_clear' => 'Вы уверены что хотите удалить все слова?',
        'empty_words'   => 'Слов еще нет!',
        'enter_word'    => 'Введите слово',

        'not_enter_word' => 'Вы не ввели слово для занесения в список!',
        'word_listed'    => 'Введенное слово уже имеется в списке!',
        'owner_clear'    => 'Очищать список может только владелец!',
    ],

    'backup' => [
        'create_backup'   => 'Создать бэкап',
        'total_backups'   => 'Всего бэкапов',
        'empty_backups'   => 'Бэкапов еще нет!',
        'total_tables'    => 'Всего таблиц',
        'records'         => 'Записей',
        'size'            => 'Размер',
        'compress_method' => 'Метод сжатия',
        'not_compress'    => 'Не сжимать',
        'compress_ratio'  => 'Степень сжатия',
        'empty_tables'    => 'Нет таблиц для бэкапа!',

        'no_tables_save'           => 'Не выбраны таблицы для сохранения!',
        'wrong_compression_method' => 'Неправильный метод сжатия!',
        'wrong_compression_ratio'  => 'Неправильная степень сжатия!',
        'database_success_saved'   => 'База данных успешно обработана и сохранена!',
        'backup_not_indicated'     => 'Не указано название бэкапа для удаления!',
        'invalid_backup_name'      => 'Недопустимое название бэкапа!',
        'backup_not_exist'         => 'Файла для удаления не существует!',
        'backup_success_deleted'   => 'Бэкап успешно удален!',
    ],

    'banhists' => [
        'history'       => 'История',
        'search_user'   => 'Поиск по пользователю',
        'empty_history' => 'Истории банов еще нет!',
        'view_history'  => 'Просмотр истории',
    ],

    'bans' => [
        'login_hint'    => 'Введите логин пользователя который необходимо отредактировать',
        'user_ban'      => 'Бан пользователя',
        'change_ban'    => 'Изменение бана',
        'time_ban'      => 'Время бана',
        'banned'        => 'Забанить',
        'ban_hint'      => 'Внимание! Постарайтесь как можно подробнее описать причину бана',
        'confirm_unban' => 'Вы действительно хотите разбанить пользователя?',

        'forbidden_ban'      => 'Запрещено банить администрацию сайта!',
        'user_banned'        => 'Данный пользователь уже заблокирован!',
        'user_not_banned'    => 'Данный пользователь не забанен!',
        'time_not_indicated' => 'Вы не указали время бана!',
        'time_not_selected'  => 'Не выбрано время бана!',
        'time_empty'         => 'Слишком маленькое время бана!',
        'success_banned'     => 'Пользователь успешно забанен!',
        'success_unbanned'   => 'Пользователь успешно разбанен!',
    ],

    'blacklists' => [
        'email'      => 'Email',
        'logins'     => 'Логины',
        'domains'    => 'Домены',
        'empty_list' => 'Список еще пуст!',

        'type_not_found' => 'Указанный тип не найден!',
        'invalid_login'  => 'Недопустимые символы в логине!',
    ],

    'caches' => [
        'files'           => 'Файлы',
        'images'          => 'Изображения',
        'clear'           => 'Очистить кэш',
        'empty_files'     => 'Файлов еще нет!',
        'empty_images'    => 'Изображений еще нет!',
        'success_cleared' => 'Кеш успешно очищен!',
    ],

    'chat' => [
        'clear'            => 'Очистить чат',
        'confirm_clear'    => 'Вы действительно хотите очистить админ-чат?',
        'edit_message'     => 'Редактирование сообщения',
        'post_added_after' => 'Добавлено через :sec сек.',
        'success_cleared'  => 'Админ-чат успешно очищен!',
    ],

    'checkers' => [
        'new_files'          => 'Новые файлы и новые параметры файлов',
        'old_files'          => 'Удаленные файлы и старые параметры файлов',
        'empty_changes'      => 'Нет изменений!',
        'initial_scan'       => 'Необходимо провести начальное сканирование!',
        'information_scan'   => 'Сканирование системы позволяет узнать какие файлы или папки менялись в течение определенного времени',
        'invalid_extensions' => 'Внимание, сервис не учитывает некоторые расширения файлов',
        'scan'               => 'Сканировать',
        'success_crawled'    => 'Сайт успешно просканирован!',
    ],

    'delivery' => [
        'online'                  => 'В онлайне',
        'active'                  => 'Активным',
        'admins'                  => 'Администрации',
        'users'                   => 'Всем пользователям',
        'not_recipients_selected' => 'Вы не выбрали получаетелей рассылки!',
        'not_recipients'          => 'Отсутствуют получатели рассылки!',
        'success_sent'            => 'Сообщение успешно разослано!',
    ],

    'delusers' => [
        'condition'         => 'Удалить пользователей которые не посещали сайт',
        'minimum_asset'     => 'Минимум актива',
        'deleted_condition' => 'Будут удалены пользователи не посещавшие сайт более',
        'asset_condition'   => 'И имеющие в своем активе не более',
        'deleted_users'     => 'Будет удалено пользователей',
        'delete_users'      => 'Удалить пользователей',
        'invalid_period'    => 'Указанно недопустимое время для удаления!',
        'users_not_found'   => 'Отсутствуют пользователи для удаления!',
        'success_deleted'   => 'Пользователи успешно удалены!',
    ],

    'errors' => [
        'hint'            => 'Внимание! Запись логов выключена в настройках!',
        'errors'          => 'Ошибки :code',
        'autobans'        => 'Автобаны',
        'logs_not_exist'  => 'Указанные логи не существуют!',
        'success_cleared' => 'Логи успешно очищены!',
    ],

    'files' => [
        'confirm_delete_dir'        => 'Вы действительно хотите удалить эту директорию?',
        'confirm_delete_file'       => 'Вы действительно хотите удалить этот файл?',
        'objects'                   => 'Объектов',
        'lines'                     => 'Строк',
        'changed'                   => 'Изменен',
        'empty_objects'             => 'Объектов нет!',
        'create_object'             => 'Создание нового объекта',
        'directory_name'            => 'Название директории',
        'create_directory'          => 'Создать директорию',
        'file_name'                 => 'Название файла (без расширения)',
        'create_file'               => 'Создать файл',
        'create_hint'               => 'Разрешены латинские символы и цифры, а также знаки дефис и нижнее подчеркивание',
        'file_editing'              => 'Редактирование файла',
        'edit_hint'                 => 'Нажмите Ctrl+Enter для перевода строки, Shift+Enter для вставки линии',
        'writable'                  => 'Внимание! Файл недоступен для записи!',
        'file_not_exist'            => 'Данного файла не существует!',
        'directory_not_exist'       => 'Данной директории не существует!',
        'directory_not_writable'    => 'Директория :dir недоступна для записи!',
        'file_required'             => 'Необходимо ввести название файла!',
        'directory_required'        => 'Необходимо ввести название директории!',
        'file_invalid'              => 'Недопустимое название файла!',
        'directory_invalid'         => 'Недопустимое название директории!',
        'file_success_saved'        => 'Файл успешно сохранен!',
        'file_success_created'      => 'Новый файл успешно создан!',
        'directory_success_created' => 'Новая директория успешно создана!',
        'file_success_deleted'      => 'Файл успешно удален!',
        'directory_success_deleted' => 'Директория успешно удалена!',
        'file_exist'                => 'Файл с данным названием уже существует!',
        'directory_exist'           => 'Директория с данным названием уже существует!',
    ],

    'invitations' => [
        'hint'                 => 'Регистрация по приглашения выключена!',
        'unused'               => 'Неиспользованные',
        'used'                 => 'Использованные',
        'owner'                => 'Владелец',
        'invited'              => 'Приглашенный',
        'create_keys'          => 'Создать ключи',
        'list_keys'            => 'Список ключей',
        'empty_invitations'    => 'Приглашений еще нет!',
        'creation_keys'        => 'Создание ключей',
        'key_generation'       => 'Генерация новых ключей',
        'send_to_user'         => 'Отправить ключ пользователю',
        'sending_keys'         => 'Рассылка ключей',
        'send_to_active_users' => 'Разослать ключи активным пользователям',
        'my_keys'              => 'Мои ключи',
        'empty_keys'           => 'У вас нет пригласительных ключей!',
    ],

    'ipbans' => [
        'history'       => 'История автобанов',
        'empty_ip'      => 'В бан-листе пока пусто!',
        'hint' => '
        Примеры банов: 127.0.0.1 без отступов и пробелов<br>
        Или по маске 127.0.0.* , 127.0.*.* , будут забанены все IP совпадающие по начальным цифрам',
        'confirm_clear' => 'Вы действительно хотите очистить список IP?',
    ],

    'logs' => [
        'page'          => 'Страница',
        'referer'       => 'Откуда',
        'confirm_clear' => 'Вы уверены что хотите очистить логи?',
        'empty_logs'    => 'Логов еще нет!',
    ],

    'modules' => [
        'module'         => 'Модуль',
        'migrations'     => 'Миграции',
        'symlink'        => 'Симлинк',
        'empty_modules'  => 'Модули еще не загружены!',
        'confirm_delete' => 'Вы действительно хотите удалить модуль?',
        'hint'           => 'Внимание! При удалении модуля, будут удалены все миграции и изменения в БД',
    ],

    'notices' => [
        'confirm_delete'       => 'Вы действительно хотите удалить данный шаблон?',
        'empty_notices'        => 'Шаблонов еще нет!',
        'edit'                 => 'Редактирование шаблона',
        'edit_system_template' => 'Вы редактируете системный шаблон',
        'system_template'      => 'Системный шаблон',
        'create'               => 'Создание шаблона',
    ],

    'reglists' => [
        'enabled'     => 'Подтверждение регистраций включено!',
        'disabled'    => 'Подтверждение регистрации выключено!',
        'empty_users' => 'Нет пользователей требующих подтверждения регистрации!',
    ],

    'rules' => [
        'empty_rules'   => 'Правила сайта еще не установлены!',
        'editing_rules' => 'Редактирование правил',
        'variables'     => 'Внутренние переменные',
        'sitename'      => 'Название сайта',
    ],

    'spam' => [
        'go_to_message'     => 'Перейти к сообщению',
        'empty_spam'        => 'Жалоб еще нет!',
    ],

    'status' => [
        'list'           => 'Список статусов',
        'confirm_delete' => 'Вы уверены что хотите удалить выбранный статус?',
        'empty_statuses' => 'Статусы еще не назначены!',
        'edit_status'    => 'Редактирование статуса',
        'create_status'  => 'Создание статуса',
    ],
];
