<?php
header("Content-type: text/html; charset=UTF-8");
if (isset($_POST['request']))
{
    //ここに何かしらの処理を書く（DB登録やファイルへの書き込みなど）
    function test()
    {
        return 0;
    }
    test();
    echo "string";
}
else
{
    echo 'The parameter of "request" is not found.';
}
?>