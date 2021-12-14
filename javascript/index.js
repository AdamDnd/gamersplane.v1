$(function() {

    var storage = window.localStorage;
    $('.announcements .openClose').on('click',function(){
        var pThis=$(this);
        pThis.toggleClass('openClose-open');
        var announements=pThis.closest('.announcements');
        $('.announcementPost',announements).slideToggle();
        announements.toggleClass('annoucementClosed');

        var storageKey='announcement-'+pThis.data('announce')+'-'+pThis.data('threadid');
        if(pThis.hasClass('openClose-open')){
            storage.removeItem(storageKey);
        } else {
            storage.setItem(storageKey, pThis.data('threadid'));
        }
    });

    $('.announcements .openClose').each(function(){
        var pThis=$(this);
        var threadId=pThis.data('threadid');
        var storageKey='announcement-'+pThis.data('announce')+'-'+threadId;
        if(storage.getItem(storageKey)==threadId){
            pThis.click();
        }

    });

    $('.notifyThread a').on('click',function(event){
        var navigateTo=$(this).attr('href');
        event.preventDefault();
        var postId=$(this).data('postid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/users/removeThreadNotification',
			xhrFields: {
				withCredentials: true
			},
			data:{ postID: postId}
		}).always(function() {
            location.href = navigateTo;
        });
    });

    $('<span style="float:right;cursor:pointer;"><img src="/images/sleepingWhite.png" title="Snooze games" class="snoozeToggle"/></span>').appendTo('#yourGames h3.gamesheaderbar').on('click',function(){
        $('#yourGames').toggleClass('snoozeMode');
        if($('#yourGames').hasClass('snoozeMode')){
            $('#yourGames .sudoTable .tr .name').each(function(){
                $('<img src="/images/sleeping.png" class="snoozer"/>').prependTo($(this));
            });
        }else{
            $('#yourGames .sudoTable .tr .snoozer').remove();
            var snoozeUrls=[];
            $('#yourGames .sudoTable .tr.snoozed').each(function(){
                snoozeUrls.push($('.name a',this).attr('href'));
            });
            storage.setItem('snoozeGames', JSON.stringify(snoozeUrls));
        }
    });

    $('#yourGames').on('click','.snoozer',function(){
        $(this).closest('.tr').toggleClass('snoozed');
    });

    var snoozedGames=storage.getItem('snoozeGames');
    if(snoozedGames){
        var snoozeGames=JSON.parse(snoozedGames);
        for(var i=0;i<snoozeGames.length;i++){
            $('#yourGames .sudoTable .tr.noPosts:has(.name a[href="'+snoozeGames[i]+'"])').addClass('snoozed');
        }
    }

});
