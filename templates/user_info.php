<!DOCTYPE html>
<html>
<head>
    <title>ユーザー登録</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<form action="/user/create/info" method="POST">
ユーザー名：<input type="text" name="user_name">
メールアドレス：<input type="text" name="mail_address">
password：<input type="password" name="user_password">

<input type="submit" value="登録">
</form>
</body>
</html>