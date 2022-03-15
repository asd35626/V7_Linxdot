
if (document.getElementById('searchFormObj')) {

    document.getElementById('searchFormObj').onsubmit = checkAccountSearch;
  

}

// 檢查人資報表的搜尋欄位，如果有問題將不會做任何處理!
function checkAccountSearch() {

    var IfError = false;
    if (document.getElementById('searchFormObj')) {

        if (document.newgameform.CreateDateFrom.value != "" && document.newgameform.CreateDateTo.value != "")
        {
            if (document.newgameform.CreateDate.value.length < 4 && document.newgameform.CreateDate.value != "") {
                alert('請輸入4位數數字');
                IfError = true;
            }
            else
            {
                IfError = false;
            }
            
        }

        else if (document.newgameform.ProductName.value != "")
        {
            if (document.newgameform.CreateDate.value.length < 4 && document.newgameform.CreateDate.value != "") {
                alert('請輸入4位數數字');
                IfError = true;
            }
            else {
                IfError = false;
            }
        }

        else if (document.newgameform.ProductName.value == "" && document.newgameform.CreateDateFrom.value == "" && document.newgameform.CreateDateTo.value == "")
        {
            if (document.newgameform.CreateDate.value.length < 4 && document.newgameform.CreateDate.value != "") {
                alert('請輸入4位數數字');
                IfError = true;
            }
            else {
                IfError = false;
            }
        }

        else if (document.newgameform.ProductName.value != "" && document.newgameform.CreateDateFrom.value != "" && document.newgameform.CreateDateTo.value != "")
        {
            if (document.newgameform.CreateDate.value.length < 4 && document.newgameform.CreateDate.value != "") {
                alert('請輸入4位數數字');
                IfError = true;
            }
            else {
                IfError = false;
            }
        }
    }

    if (IfError == true)
    {
         return false;
    }
    else
    {
        return true;
    }

   
}
