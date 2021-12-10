$(function() {
    var curText=$.trim($('.markItUpEditor').val());
    var threadId=parseInt($('#typingThreadId').text());

    var checkForChange=function(){
        var newText=$.trim($('.markItUpEditor').val());
        var typingStatus=0;
        if(newText.length>0){
            typingStatus=1;
            if(newText!=curText){
                typingStatus=2;
            }
        }

        $.ajax({
            type: 'post',
            url: API_HOST +'/forums/typingIndicator',
            xhrFields: {
                withCredentials: true
            },
            data:{ threadId: threadId, typingStatus:typingStatus},
            success:function (data) {
                if(data && Array.isArray(data) && data.length>0){
                    $('#typingIndicator').text(data).css("visibility", "visible");
                }else{
                    $('#typingIndicator').css("visibility", "hidden");
                }

                curText=newText;
                setTimeout(checkForChange,15000);
            }
        });
    };

    setTimeout(checkForChange,1000);

});