
// 選擇所有的訂單

var thisSelectedOrderValue = '';

function selectAllOrders() {

    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

         //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            orderCheckObjects[i].checked = true;

        }

    }


}
function selectAllOrdersConfirm() {

    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            orderCheckObjects[i].checked = true;

            if (orderCheckObjects[i].checked == true)
            {
                selectObjs++;
            }

        }
        //alert('Total checkCount is :' + checkCount);

        if (selectObjs == 0) {

            alert('請選擇訂單!');

        } else
        {
            document.getElementById('checkValue').value = selectObjs;
        } 
       

    }


}
function sendDeliverOrder() {

    thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        // alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要發送的通知!');

        } else {

            if (document.getElementById('deliverFormArea')) {

                // alert(document.getElementById('deliverFormArea'));
                document.deliverForm.OrderID.value = thisSelectedOrderValue;
                //document.getElementById('deliverFormArea').style.display = '';
                generateReference('#deliverFormArea');
            }



        }

    }

}

function CompletePay() {


    thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要完成的訂單!');

        } else {

           
            if (confirm('確定要完成付款嗎?')) {

                document.CompletePayment.OrderID.value = thisSelectedOrderValue;
                document.CompletePayment.submit();
             
            }
        
        }

    }

}
function PDConfirm() {


    thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要完成的訂單!');

        } else {


            if (confirm('確定要完成訂單嗎?')) {

                document.PDConfirmOrder.OrderID.value = thisSelectedOrderValue;
                document.PDConfirmOrder.submit();

            }

        }

    }

}
function Deliver() {


    thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要完成的訂單!');

        } else {


            if (confirm('確定要寄出貨物嗎?')) {

                document.DeliverProduct.OrderID.value = thisSelectedOrderValue;
                document.DeliverProduct.submit();

            }

        }

    }

}
function FSConfirm() {


    thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要完成的訂單!');

        } else {


            if (confirm('確定要處理訂單嗎?')) {

                document.FSConfirmOrder.OrderID.value = thisSelectedOrderValue;
                document.FSConfirmOrder.submit();

            }

        }

    }

}

function cancelMailForm() {

    var ifchecked = true;

    if (document.deliverForm.LetterContent.value != '') {

        if (!confirm('您已經填寫的到貨通知，確定要取消發送?')) {
            ifchecked = false;
        }

    }

    if (ifchecked) {

        document.deliverForm.LetterContent.value = '';
        CloseRefBox();
    }

}


function confirmSendDeliverMail() {

    if (confirm('確定要開始發送通知?')) {

        document.deliverForm.submit();
        $.unblockUI();

    }

}

function generateReference(refId) {

    $.blockUI({
        message: $(refId),
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#fafafa',
            '-webkit-border-radius': '5px',
            '-moz-border-radius': '5px',
            color: '#000',
            top: ($(window).height() - 500) / 2 + 'px',
            left: ($(window).width() - 670) / 2 + 'px',
            width: '650px',
            height: '300px',
            padding: '10px',
            overflow: 'auto'
        }
    });


}

function CloseRefBox() {

    $.unblockUI();

}

function sendToERP() {

    var thisSelectedOrderValue = '';
    var orderCheckObjects = null;

    orderCheckObjects = document.getElementsByName('OrderID');

    if (orderCheckObjects != null) {

        var objLength = orderCheckObjects.length;
        var i = 0;
        var selectObjs = 0;

        //alert('Total select lengths is :' + objLength);

        for (i = 0; i < objLength; i++) {

            if (orderCheckObjects[i].checked == true) {

                selectObjs++;

                if (thisSelectedOrderValue == '') {

                    thisSelectedOrderValue = orderCheckObjects[i].value;

                } else {

                    thisSelectedOrderValue += ',' + orderCheckObjects[i].value;

                }


            }

        }

        if (selectObjs == 0) {

            alert('請選擇要傳輸的訂單!');

        } else {


            if (confirm('確定要進行傳輸嗎?')) {

                document.SendToERPForm.OrderID.value = thisSelectedOrderValue;
                document.SendToERPForm.submit();

            }

        }

    }

}
