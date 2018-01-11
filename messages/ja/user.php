<?php

return [

    // models
    'ID' => '',
    'User ID' => 'ユーザID',
    'Created At' => '作成日時',
    'Updated At' => '更新日時',
    'Full Name' => '氏名',

    'Name' => '名前',
    'Can Admin' => '管理者',

    'Role' => '役割',
    'Role ID' => '役割ID',
    'Status' => '状態',
    'Email' => 'メールアドレス',
    'New Email' => '新しいメールアドレス',
    'Username' => 'ユーザ名',
    'Password' => 'パスワード',
    'Auth Key' => '承認キー',
    'Access Token' => 'アクセストークン',
    'Logged In Ip' => 'ログイン元IP',
    'Logged In At' => 'ログイン日時',
    'Created Ip' => '作成元IP',
    'Banned At' => '凍結日時',
    'Banned Reason' => '凍結理由',
    'Current Password' => '現在のパスワード',
    'New Password' => '新しいパスワード',
    'New Password Confirm' => '新しいパスワードの確認入力',
    'Email Confirmation' => 'メールアドレスの確認',

    'Provider' => 'プロバイダ',
    'Provider ID' => 'プロバイダID',
    'Provider Attributes' => 'プロバイダ属性',

    'Type' => '種別',
    'Token' => 'トークン',
    'Expired At' => '有効期限',

    // models/forms
    'Email not found' => 'メールアドレスが登録されていません',
    'Email / Username' => 'メールアドレス／ユーザ名',
    'Email / Username not found' => 'メールアドレス／ユーザ名が登録されていません',
    'Username not found' => 'ユーザ名が登録されていません',
    'User is banned - {banReason}' => 'このアカウントは凍結されています - {banReason}',
    'Incorrect password' => 'パスワードが違います',
    'Remember Me' => 'ログイン状態を保持する',
    'Email is already active' => 'このメールアドレスはすでに確認済みです',
    'Passwords do not match' => 'パスワードの確認入力が一致しません',
    '{attribute} can contain only letters, numbers, and "_"' => '{attribute} に使える文字は英数字とアンダーバー (_) です',

    // controllers
    'Successfully registered [ {displayName} ]' => 'ユーザ登録が完了しました [ {displayName} ]',
    ' - Please check your email to confirm your account' => ' - メールに書かれた手順を確認してアカウント登録を完了してください',
    'Account updated' => 'アカウント情報を更新しました',
    'Profile updated' => 'プロフィールを更新しました',
    'Confirmation email resent' => '確認メールの再送信',
    'Email change cancelled' => 'メールアドレスの変更をキャンセルしました',
    'Instructions to reset your password have been sent' => 'パスワードの再設定方法を送信しました',

    // mail
    'Please confirm your email address by clicking the link below:' => 'メールアドレス確認のため、次のリンクをクリックしてください。',
    'Please use this link to reset your password:' => 'パスワードを再設定するには、次のリンクをクリックしてください。',

    // admin views
    'Users' => 'ユーザ',
    'Banned' => '凍結',
    'Create' => '作成',
    'Update' => '更新',
    'Delete' => '削除',
    'Search' => '検索',
    'Reset' => 'リセット',
    'Create {modelClass}' => '{modelClass}の作成',
    'Update {modelClass}: ' => '{modelClass}の更新: ',
    'Are you sure you want to delete this item?' => 'この項目を削除してもよろしいですか？',

    // default views
    'Account' => 'アカウント',
    'Pending email confirmation: [ {newEmail} ]' => 'メールアドレスを確認中: [ {newEmail} ]',
    'Cancel' => 'キャンセル',
    'Changing your email requires email confirmation' => 'メールアドレスを変更するには、メールが受信できるか確認する必要があります',
    'Confirmed' => '確認済み',
    'Error' => 'エラー',
    'Your email [ {email} ] has been confirmed' => 'メールアドレス [ {email} ] が確認できました',
    'Go to my account' => 'アカウントページへ',
    'Go home' => 'ホームへ',
    'Log in here' => 'ログイン',
    'Invalid Token' => 'トークンが間違っています',
    'Forgot password' => 'パスワードを忘れた場合',
    'Submit' => '送信',
    'Yii 2 User' => 'Yii 2 ユーザ',
    'Login' => 'ログイン',
    'Register' => '登録',
    'Logout' => 'ログアウト',
    'Resend confirmation email' => '確認メールを再送信する',
    'Profile' => 'プロフィール',
    'Resend' => '再送信',
    'Password has been reset' => 'パスワードを再設定しました',
    'Login link sent - Please check your email' => 'ログイン先 URL を送信しました。メールをご確認ください',
    'Registration link sent - Please check your email' => 'アカウント登録のための URL を送信しました。メールをご確認ください',
];
