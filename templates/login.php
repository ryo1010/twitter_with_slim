<!DOCTYPE html>
<html>
<head>
    <title>ログイン画面</title>
</head>
<body>
<form action="/login" method="POST">
    <table>
        <tr>
            <td><?= $info ?></td>
        </tr>
        <tr>
            <td>mail address<input type="text" name="mail_address"></td>
        </tr>
        <tr>
            <td>password：<input type="password" name="password" ></td>
        </tr>
    </table>
<input type="submit" value="ログイン">
</form>
<form action="/user/create/mail" method="GET">
<input type="submit" value="新規作成">
</form>
</body>
</html>