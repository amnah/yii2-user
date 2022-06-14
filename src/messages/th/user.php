<?php

return [

    // models
    'ID' => 'รหัส',
    'User ID' => 'รหัสผู้ใช้',
    'Created At' => 'เวลาสร้าง',
    'Updated At' => 'เวลาปรับปรุง',
    'Full Name' => 'ชื่อเต็ม',

    'Name' => 'ชื่อ',
    'Can Admin' => 'เป็นผู้ดูแลระบบ',

    'Role' => 'กฏ',
    'Role ID' => 'รหัสกฏ',
    'Status' => 'สถานะ',
    'Email' => 'อีเมลล์',
    'New Email' => 'อีเมลล์ใหม่',
    'Username' => 'ชื่อผู้ใช้',
    'Password' => 'รหัสผ่าน',
    'Auth Key' => 'รหัส Auth',
//    'Access Token' => '',
    'Logged In Ip' => 'IP เข้าระบบ',
    'Logged In At' => 'เวลาเข้าระบบ',
    'Created Ip' => 'IP ที่สร้าง',
    'Banned At' => 'เวลาแบน',
    'Banned Reason' => 'เหตุผลที่แบน',
    'Current Password' => 'รหัสผ่านปัจจุบัน',
    'New Password' => 'รหัสผ่านใหม่',
    'New Password Confirm' => 'ยืนยันรหัสผ่านใหม่',
    'Email Confirmation' => 'ยืนยันอีเมลล์',

    'Provider' => 'ผู้ให้บริการ',
    'Provider ID' => 'รหัสผู้ให้บริการ',
    'Provider Attributes' => 'รายการผู้ให้บริการ',

    'Type' => 'ประเภท',
//    'Token' => '',
    'Expired At' => 'เวลาหมดอายุ',

    // models/forms
    'Email not found' => 'ไม่พบอีเมลล์',
    'Email / Username' => 'อีเมลล์/ชื่อผู้ใช้',
    'Email / Username not found' => 'ไม่พบอีเมลล์/ชื่อผู้ใช้',
    'Username not found' => 'ไม่พบชื่อผู้ใช้',
    'User is banned - {banReason}' => 'ผู้ใช้โดนแบน - {banReason}',
    'Incorrect password' => 'รหัสผ่านไม่ถูกต้อง',
    'Remember Me' => 'จำไว้ในระบบ',
    'Email is already active' => 'อีเมลล์นี้พร้อมใช้งานแล้ว',
    'Passwords do not match' => 'รหัสผ่านไม่ตรงกัน',
    '{attribute} can contain only letters, numbers, and "_"' => '{attribute} กำหนดให้มีตัวอักษร ตัวเลข และ "_"',

    // controllers
    'Successfully registered [ {displayName} ]' => 'การลงทะเบียนเสร็จสมบูรณ์ [ {displayName} ]',
    ' - Please check your email to confirm your account' => ' - กรุณาตรวจสอบอีเมลล์เพื่อยืนยันบัญชี',
    'Account updated' => 'บัญชีปรับปรุงแล้ว',
    'Profile updated' => 'ข้อมูลส่วนตัวปรับปรุงแล้ว',
    'Confirmation email resent' => 'ส่งข้อมูลการยืนยันที่อีเมลล์อีกครั้ง',
    'Email change cancelled' => 'การเปลี่ยนอีเมลล์ถูกยกเลิก',
    'Instructions to reset your password have been sent' => 'คำสั่งการรีเซ็ตรหัสผ่านของคุณถูกส่ง',

    // mail
    'Please confirm your email address by clicking the link below:' => 'กรุณายืนยันอีเมลล์โดยคลิกลิ้งด้านล่าง:',
    'Please use this link to reset your password:' => 'กรุณาใช้ลิ้งค์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ:',

    // admin views
    'Users' => 'ผู้ใช้งาน',
    'Banned' => 'ถูกแบน',
    'Create' => 'สร้าง',
    'Update' => 'ปรับปรุง',
    'Delete' => 'ลบ',
    'Search' => 'ค้นหา',
    'Reset' => 'เริ่มใหม่',
    'Create {modelClass}' => 'สร้าง {modelClass}',
    'Update {modelClass}: ' => 'ปรับปรุง {modelClass}: ',
    'Are you sure you want to delete this item?' => 'คุณแน่ใจนะว่าต้องการลบสิ่งนี้?',

    // default views
    'Account' => 'บัญชี',
    'Pending email confirmation: [ {newEmail} ]' => 'ยังไม่มีการยืนยันอีเมลล์: [ {newEmail} ]',
    'Cancel' => 'ยกเลิก',
    'Changing your email requires email confirmation' => 'กำลังเปลี่ยนอีเมลล์สำหรับการยืนยัน',
    'Confirmed' => 'ยืนยันแล้ว',
    'Error' => 'ผิดพลาด',
    'Your email [ {email} ] has been confirmed' => 'อีเมลล์ [ {email} ได้รับการยืนยันแล้ว',
    'Go to my account' => 'ไปที่บัญชีของฉัน',
    'Go home' => 'ไปหน้าหลัก',
    'Log in here' => 'เข้าสู่ระบบที่นี่',
//    'Invalid Token' => '',
    'Forgot password' => 'ลืมรหัสผ่าน',
    'Submit' => 'ส่งข้อมูล',
    'Yii 2 User' => 'Yii 2 User',
    'Login' => 'เข้าสู่ระบบ',
    'Register' => 'ลงทะเบียน',
    'Logout' => 'ออกจากระบบ',
    'Resend confirmation email' => 'ส่งอีเมลล์ยืนยันอีกครั้ง',
    'Profile' => 'ข้อมูลผู้ใช้',
    'Resend' => 'ส่งอีกครั้ง',
    'Password has been reset' => 'รหัสผ่านถูกรีเซ็ต',
//    'Login link sent - Please check your email' => '',
//    'Registration link sent - Please check your email' => '',
];