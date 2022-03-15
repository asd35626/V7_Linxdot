
function documentDelete(adid,adcid,fromadcid,type) {

    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?ADID=' + adid + '&ADCID=' + adcid + '&FromADCID=' + fromadcid + '&Type=' + type;

}
function documentSubCategoryDelete(adcid, fromadcid) {
    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?ADCID=' + adcid + '&FromADCID=' + fromadcid;

}
function documentCategoryDelete(adcid, fromadcid) {

    console.log(fromadcid);

    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?ADCID=' + adcid + '&FromADCID=' + fromadcid;

}
function userDelete(userID) {
    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?UID=' + userID;
}
function organizationDelete(organizationID) {
    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?OrgID=' + organizationID;
}
function organizationEdit(organizationID) {
    window.location.href = 'SingleDataTable.aspx?Action=Edit&OrgID=' + organizationID;

}
function adminUserDelete(userID) {

    if (confirm('確定要刪除這筆資料?')) {
        document.subItemForm2.UID.value = userID;
        document.subItemForm2.submit();
    }
}
function replyDelete(replyID, adid) {
    console.log(replyID);
    console.log(adid);
    if (confirm('確定要刪除這筆資料?'))
        window.location.href = 'SingleDataDelete.aspx?ReplyID=' + replyID + '&ADID=' + adid;
}

