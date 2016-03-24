<form action="/tweet/edit" method="POST">
    <table border='0'>
    <tr>
        <td>
            ユーザー名：<?= $rows['user_name'] ?>
        </td>
    </tr>
    <tr>
        <td>
        ツイート時間<?= $rows['created_at']?>
        </td>
    </tr>
    <tr>
        <td>
        <!-- 画像があるかないかの判断書く！ -->
        <img src="/images/<?= $rows['images_url']?>" width=300>
        </td>
    <tr>    <tr>
        <td>
        <TEXTAREA name="tweet_content" cols="40" rows="5"><?= $rows['content']?></TEXTAREA>
        </td>
    <tr>
        <td>
        <input type="hidden" name="tweet_id" value="<?= $rows['tweet_id']?>">
        </td>
    </tr>
    </table>
    <input type="submit" name="tweet_edit" value="編集">
</form>
<form action="/tweet/delete" method="POST">
    <input type="hidden" name="tweet_id" value="<?= $rows['tweet_id']?>">
    <input type="submit" name="tweet_delete" value="削除"></input>
</form>
</body>
</html>