<?php

return [

    // models
    'ID' => 'ID',
    'User ID' => '用户 ID',
    'Create Time' => '创建时间',
    'Update Time' => '更新时间',
    'Full Name' => '全名',

    'Name' => '名称',
    'Can Admin' => '是否管理员',

    'Role' => '角色',
    'Role ID' => '角色 ID',
    'Status' => '状态',
    'Email' => '邮箱',
    'New Email' => '新邮箱',
    'Username' => '用户名',
    'Password' => '密码',
    'Auth Key' => 'Auth Key',
    'Api Key' => 'Api Key',
    'Login Ip' => '登陆 IP',
    'Login Time' => '登陆时间',
    'Create Ip' => '创建 IP',
    'Ban Time' => '封号时间',
    'Ban Reason' => '封号原因',
    'Current Password' => '当前密码',
    'New Password' => '新密码',
    'New Password Confirm' => '确认新密码',
    'Email Confirmation' => '验证邮件',

    'Provider' => 'Provider',
    'Provider ID' => 'Provider ID',
    'Provider Attributes' => 'Provider Attributes',

    'Type' => '类型',
    'Key' => 'Key',
    'Consume Time' => '使用时间',
    'Expire Time' => '超时时间',

    // models/forms
    'Email not found' => '该邮箱尚未注册',
    'Email / Username' => '邮箱 / 用户名',
    'Email / Username not found' => '邮箱 / 用户名 不存在',
    'Username not found' => '该用户名尚未注册',
    'User is banned - {banReason}' => '用户已封 - {banReason}',
    'Incorrect password' => '错误的密码',
    'Remember Me' => '记住登陆状态',
    'Email is already active' => '该邮箱已经被激活，无需再激活',
    'Passwords do not match' => '两个密码不一致',
    '{attribute} can contain only letters, numbers, and "_"' => '{attribute} 只能包含字母、数字和下划线_',

    // controllers
    'Successfully registered [ {displayName} ]' => '[ {displayName} ] 注册成功！',
    ' - Please check your email to confirm your account' => '请到您的邮箱激活账户',
    'Account updated' => '账号已更新',
    'Profile updated' => '个人资料已更新',
    'Confirmation email resent' => '验证邮件已发送',
    'Email change cancelled' => '已取消更改邮箱地址得请求',
    'Instructions to reset your password have been sent' => '重置密码邮件已发送',

    // mail
    'Please confirm your email address by clicking the link below:' => '请点击下面链接以认证您的邮箱地址：',
    'Please use this link to reset your password:' => '请使用该链接来重置您的密码：',

    // admin views
    'Users' => '用户',
    'Banned' => '封号',
    'Create' => '创建',
    'Update' => '更新',
    'Delete' => '删除',
    'Search' => '搜索',
    'Reset' => '重置',
    'Create {modelClass}' => '创建 {modelClass}',
    'Update {modelClass}: ' => '更新 {modelClass}: ',
    'Are you sure you want to delete this item?' => '确定要删除该项目吗？',

    // default views
    'Account' => '账号',
    'Pending email confirmation: [ {newEmail} ]' => '等待邮件验证: [ {newEmail} ]',
    'Cancel' => '取消',
    'Changing your email requires email confirmation' => '更改邮箱地址，需要重新邮件验证',
    'Confirmed' => '验证',
    'Error' => '错误',
    'Your email [ {email} ] has been confirmed' => '恭喜！你的邮箱地址 [ {email} ] 已经激活成功！',
    'Go to my account' => '点击进入我的账户',
    'Go home' => '点击进入首页',
    'Log in here' => '点击此处登陆',
    'Invalid key' => 'key无效',
    'Forgot password' => '忘记密码',
    'Submit' => '提交',
    'Yii 2 User' => 'Yii 2 用户',
    'Login' => '登陆',
    'Register' => '注册',
    'Logout' => '退出', // i dont think this is used ...
    'Resend confirmation email' => '重新发送验证邮件',
    'Profile' => '个人资料',
    'Please fill out the following fields to login:' => '请输入下面信息登陆',
    'Please fill out the following fields to register:' => '请输入下面信息注册',
    'Resend' => '重新发送验证邮件',
    'Password has been reset' => '已重置密码',
];