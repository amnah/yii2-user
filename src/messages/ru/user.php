<?php

return [

    // models
    'ID' => 'Номер',
    'User ID' => 'Номер пользователя',
    'Created At' => 'Дата создания',
    'Updated At' => 'Дата обновления',
    'Full Name' => 'Полное имя',

    'Name' => 'Имя',
    'Can Admin' => 'Имеет права админа',

    'Role' => 'Роль',
    'Role ID' => 'Номер роли',
    'Status' => 'Статус',
    'Email' => 'Email',
    'New Email' => 'Новый email',
    'Username' => 'Логин пользователя',
    'Password' => 'Пароль',
    'Auth Key' => 'Ключ авторизации',
//    'Access Token' => '',
    'Logged In Ip' => 'IP при авторизации',
    'Logged In At' => 'Дата авторизации',
    'Created Ip' => 'IP при регистрации',
    'Banned At' => 'Дата бана',
    'Banned Reason' => 'Причина бана',
    'Current Password' => 'Текущий пароль',
    'New Password' => 'Новый пароль',
//    'New Password Confirm' => '',
    'Email Confirmation' => 'Подтверждение email',

    'Provider' => 'Пригласивший',
    'Provider ID' => 'Номер пригласившего пользователя',
    'Provider Attributes' => 'Параметры пригласившего пользователя',

    'Type' => 'Тип',
//    'Token' => '',
    'Expired At' => 'Истекает',

    // models/forms
    'Email not found' => 'Пользователь с таким email  не найден',
    'Email / Username' => 'Email / имя пользователя',
    'Email / Username not found' => 'Пользователь с таким email / именем не найден',
    'Username not found' => 'Пользователь с таким именем не найден',
    'User is banned - {banReason}' => 'Пользователь заблокирован - {banReason}',
    'Incorrect password' => 'Неверный пароль',
    'Remember Me' => 'Запомнить меня',
    'Email is already active' => 'Email уже подтверждён',
//    'Passwords do not match' => '',
//    '{attribute} can contain only letters, numbers, and "_"' => '',

    // controllers
    'Successfully registered [ {displayName} ]' => 'Успешно зарегистрирован [ {displayName} ]',
    ' - Please check your email to confirm your account' => ' - Проверьте почту, на неё должна прийти ссылка подтверждения аккаунта',
    'Account updated' => 'Аккаунт успешно обновлён',
    'Profile updated' => 'Профиль успешно обновлён',
    'Confirmation email resent' => 'Ссылка подтверждения была отправлена на email',
    'Email change cancelled' => 'Изменение email отменено',
    'Instructions to reset your password have been sent' => 'Инструкции по изменению пароля были отправлены вам на email',

    // mail
    'Please confirm your email address by clicking the link below:' => 'Пожалуйста, подтвердите свой email, нажав на ссылку ниже:',
    'Please use this link to reset your password:' => 'Пожалуйста, воспользуйтесь этой ссылкой для восстановления пароля:',

    // admin views
    'Users' => 'Пользователи',
    'Banned' => 'Заблокирован',
    'Create' => 'Создать',
    'Update' => 'Изменить',
    'Delete' => 'Удалить',
    'Search' => 'Поиск',
    'Reset' => 'Сбросить',
    'Create {modelClass}' => 'Создать {modelClass}',
    'Update {modelClass}: ' => 'Изменить {modelClass}: ',
    'Are you sure you want to delete this item?' => 'Вы уверены, что хотите удалить этот аккаунт?',

    // default views
    'Account' => 'Аккаунт',
    'Pending email confirmation: [ {newEmail} ]' => 'В ожидании подтверждения email: [ {newEmail} ]',
    'Cancel' => 'Отменить',
    'Changing your email requires email confirmation' => 'Изменение email требует подтверждения нового адреса',
    'Confirmed' => 'Подтверждено',
    'Error' => 'Ошибка',
    'Your email [ {email} ] has been confirmed' => 'Ваш email [ {email} ] был подтверждён',
    'Go to my account' => 'К моему аккаунту',
    'Go home' => 'На главную',
    'Log in here' => 'Войти',
//    'Invalid Token' => '',
    'Forgot password' => 'Забыли пароль',
    'Submit' => 'Отправить',
    'Yii 2 User' => 'Yii 2 пользователь',
    'Login' => 'Войти',
    'Register' => 'Зарегистрироваться',
    'Logout' => 'Выйти',
    'Resend confirmation email' => 'Повторно отправить подтверждение по email',
    'Profile' => 'Профиль',
    'Resend' => 'Повторить',
    'Password has been reset' => 'Пароль был сброшен',
//    'Login link sent - Please check your email' => '',
//    'Registration link sent - Please check your email' => '',
];
