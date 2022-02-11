function deleteHandle(event)
{
    event.preventDefault(); // 一旦フォームの動きを止める

    if(window.confirm('本当に削除しますか？'))
    {
        // 削除okならフォームを再開
        document.getElementById('delete-form').submit();
    }
    else
    {
        alert('キャンセルしました');
    }
}
