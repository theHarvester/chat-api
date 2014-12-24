// App Variables
var handleList = false;
var conversationList = false;
var refreshIntervalId = false;

var currentHandle = false;
var currentConversationId = false;
var currentConversationPartner = false;
var lastMessageTs = false;
var currentScreen = 'handles';

var playNewMsgTone = false;
var newHandleRegex = /^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/;

// jQuery References
var jqScreen;

var onHandleClick = function () {
    currentHandle = $(this).attr('data-handle');
    loadConversationScreen(currentHandle);
};

var onConversationClick = function () {
    currentConversationId = $(this).attr('data-conversation');
    currentConversationPartner = $(this).attr('data-partner');
    loadChatScreen(currentConversationId);
};

$(document).ready(function () {
    document.getElementById('msg-tone').volume = 0.4;
    $('#footer').hide();
    jqScreen = $('#screen');
    loadHandleScreen();

    $('#btn-back').click(back);

    $('#btn-send').click(sendMessage);

    // detects enter key when shift is not down
    $('.chat-input').keydown(function (event) {
        if (event.which == 13) {
            if (!event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

    });

    $('#btn-options').click(toggleMenu);
    $('#blur-overlay').click(toggleMenu);
});

function back() {
    $('#footer').hide();
    if (currentScreen == 'conversation') {
        loadHandleScreen();
    } else if (currentScreen == 'chat') {
        loadConversationScreen(currentHandle);
    } else {
        loadHandleScreen();
    }
}

function toggleMenu() {
    var slideoutMenu = $('#side-menu');
    var slideoutMenuWidth = slideoutMenu.width();

    var blurOverlay = $('#blur-overlay');

    // toggle open class
    slideoutMenu.toggleClass("open");


    // slide menu
    if (slideoutMenu.hasClass("open")) {
        slideoutMenu.animate({
            left: "0px"
        });

        blurOverlay.toggleClass("overlay-hidden");

        blurOverlay.animate({
            opacity: "0.6"
        }, {
            easing: 'swing',
            duration: 250,
            complete: function () {

            }
        });
    } else {
        slideoutMenu.animate({
            left: -slideoutMenuWidth
        }, 250);

        blurOverlay.animate({
            opacity: "0"
        }, {
            easing: 'swing',
            duration: 250,
            complete: function () {
                blurOverlay.toggleClass("overlay-hidden");
            }
        });
    }
}

function setTitle(title) {
    $('#title').text(title);
}

function loadHandleScreen() {
    currentScreen = 'handle';
    setTitle('My handles');
    getHandleList(true);
}

// Handles

function getHandleList(renderAfterUpdate) {
    $.ajax({
        url: handlesApiUrl,
        beforeSend: function (xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function (data) {
            var json = JSON.parse(data);
            if (json.handles !== 'undefined') {
                window.handleList = json.handles;
                if (renderAfterUpdate) {
                    renderHandleList(json.handles);
                }
            }
        }
    });
}

function renderHandleList(handleList) {
    jqScreen.empty();

    if (!isArrayEmpty(handleList)) {
        var div = $("<div>");
        div.addClass("handle list-elem");
        div.attr('data-handle', '__ALL');
        div.text('All conversations');
        div.click(onHandleClick);

        jqScreen.append(div);
    }

    $.each(handleList, function () {
        var div = $("<div>");
        div.addClass("handle list-elem");
        div.attr('data-handle', this);
        div.text(this);
        div.click(onHandleClick);

        jqScreen.append(div);
    });

    if (isArrayEmpty(handleList)) {
        var div = $("<div>");
        div.addClass("info-container");

        // info text
        var infoDiv = $("<div>");
        infoDiv.addClass("info");
        infoDiv.text("Handles are identities that you can use to chat with people. You can delete them at any time and stop anyone contacting you through that handle without affecting your other identities.");

        // create handle btn
        var createHandleBtn = $("<div>");
        createHandleBtn.addClass("create-handle-btn");
        createHandleBtn.text("Create handle");
        createHandleBtn.click(showCreateHandleForm);

        div.append(infoDiv);
        div.append(createHandleBtn);

        jqScreen.append(div);
    }

    // draw the add new handle button
    var createHandleCircleBtn = $("<div>");
    createHandleCircleBtn.addClass("create-circle-btn");
    createHandleCircleBtn.text("+");
    createHandleCircleBtn.click(showCreateHandleForm);
    jqScreen.append(createHandleCircleBtn);
}

// Conversations

function loadConversationScreen(handleId) {
    currentScreen = 'conversation';
    currentHandle = handleId;
    setTitle('Conversations');
    getConversationList(true);
}

function getConversationList(renderAfterUpdate) {
    $.ajax({
        url: conversationsApiUrl,
        beforeSend: function (xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function (data) {
            var json = JSON.parse(data);
            if (json.handles !== 'undefined') {
                window.conversationList = json.conversations;
                if (renderAfterUpdate) {
                    renderConversationList(json.conversations);
                }
            }
        }
    });
}

function renderConversationList(conversationList) {
    jqScreen.empty();
    $.each(conversationList, function () {
        var conversationWith = "";
        $.each(this.handles, function () {
            if (currentHandle != this) {
                conversationWith = this;
            }
        });

        var div = $("<div>");
        div.addClass("conversation list-elem");
        div.attr('data-conversation', this.id);
        div.attr('data-partner', conversationWith);
        div.text(conversationWith);
        div.click(onConversationClick);

        jqScreen.append(div);
    });

    if (isEmpty(conversationList)) {
        var div = $("<div>");
        div.addClass("info-container");

        // info text
        var infoDiv = $("<div>");
        infoDiv.addClass("info");
        infoDiv.text("This handle has no current conversations");

        // create handle btn
        var createHandleBtn = $("<div>");
        createHandleBtn.addClass("create-conversation-btn");
        createHandleBtn.text("Create conversation");
        createHandleBtn.click(showCreateConversationForm);

        div.append(infoDiv);
        div.append(createHandleBtn);

        jqScreen.append(div);
    }

    var createConvCircleBtn = $("<div>");
    createConvCircleBtn.addClass("create-circle-btn");
    createConvCircleBtn.text("+");
    createConvCircleBtn.click(showCreateConversationForm);
    jqScreen.append(createConvCircleBtn);
}

// Chat

function loadChatScreen(conversationId) {
    currentScreen = 'chat';
    setTitle('Chat with ' + currentConversationPartner);
    jqScreen.empty();

    var div = $("<div>");
    div.addClass("chat-screen");
    jqScreen.append(div);

    $('#footer').show();

    loadFirstChatData(conversationId);

    clearInterval(refreshIntervalId);
    refreshIntervalId = setInterval(loadLatestChatData, 1000);

}

function loadFirstChatData(conversationId) {
    console.log(conversationsApiUrl + '/' + conversationId);
    $.ajax({
        url: conversationsApiUrl + '/' + conversationId,
        beforeSend: function (xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function (data) {
            var json = JSON.parse(data);
            renderChat(json.messages);
            if (isArrayEmpty(json.messages)) {
                lastMessageTs = Math.round((new Date()).getTime() / 1000);
            }
        }
    });
}

function loadLatestChatData() {
    $.ajax({
        url: conversationsApiUrl + '/' + currentConversationId + '/from/' + lastMessageTs,
        beforeSend: function (xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function (data) {
            var json = JSON.parse(data);
            renderChat(json.messages);
        }
    });
}

function renderChat(messages) {
    var jqChatScreen = $(".chat-screen");

    $.each(messages, function () {
        lastMessageTs = this.ts;

        var div = $("<div>");
        if (currentHandle == this.handle) {
            div.addClass("message from-me");
        } else {
            playNewMsgTone = true;
            div.addClass("message from-them");
        }
        div.html(textEmbedNewlines(this.message));
        jqChatScreen.append(div);
    });

    if (!isArrayEmpty(messages)) {
        jqScreen.animate({scrollTop: jqScreen[0].scrollHeight}, 300);
    }

    if (playNewMsgTone) {
        document.getElementById('msg-tone').play();
        playNewMsgTone = false;
    }
}

function sendMessage() {
    var jqChatInput = $(".chat-input");
    var myMessage = jqChatInput.val();
    jqChatInput.val("");

    sendNewMessage(currentConversationId, myMessage, function(){});
}

function sendNewMessage(conversationId, message, onSuccess){
    var data = {
        "conversation_id": conversationId,
        "message": message
    };

    $.ajax({
        type: "POST",
        url: messagesApiUrl,
        data: data,
        success: function (data) {
            onSuccess(conversationId);
        }
    });
}

// Create handle

function showCreateHandleForm() {
    currentScreen = 'create-handle';

    var div = $('<div>');
    div.addClass('list-elem');

    var name = $("<input>");
    name.addClass("input-handle-name");
    name.attr('type', 'text');
    name.on('keydown', function (event) {
        if (event.which == 13) {
            input = $(this).val();
            if (input.length > 0) {
                if (newHandleRegex.test(input)) {
                    var data = {
                        "name": input
                    };

                    $.ajax({
                        type: "POST",
                        url: handlesApiUrl,
                        data: data,
                        success: function (data) {

                            // todo: check if it was successful
                            console.log(data);
                            loadHandleScreen();
                        }
                    });
                } else {
                    name.removeClass('status-success');
                    name.addClass('status-fail');
                }
            } else {
                // todo: request anonymous id
                alert('nothing entered');
            }

        }
    });
    name.on('keyup', function () {
        if (newHandleRegex.test($(this).val())) {
            name.addClass('status-success');
            name.removeClass('status-fail');
        } else {
            name.removeClass('status-success');
            name.addClass('status-fail');
        }
        if($(this).val().length == 0) {
            name.removeClass('status-success');
            name.removeClass('status-fail');
        }
    });

    div.append(name);
    jqScreen.append(name);

    name.focus();
}

// Create conversation

function showCreateConversationForm() {
    currentScreen = 'create-conversation';
    renderCreateConversationScreen();
}

function renderCreateConversationScreen() {
    jqScreen.empty();

    var select = $("<select>");
    select.addClass("handle-select");

    $.each(handleList, function(){
        var option = $("<option>");
        option.val(this);
        option.text(this);
        if(this == currentHandle){
            option.attr('selected', 'selected');
        }
        select.append(option);
    });

    var div = $("<div>");
    div.addClass("create-handle-info");
    div.text("To:");

    var divFrom = $("<div>");
    divFrom.addClass("create-handle-info");
    divFrom.text("From:");

    var name = $("<input>");
    name.addClass("input-handle-name");
    name.attr('type', 'text');

    var trigger = $("<div>");
    trigger.addClass("create-conversation-trigger");
    trigger.text("Send message");
    trigger.click(triggerCreateConversation);

    var message = $("<textarea>");
    message.addClass("create-conversation-message");

    jqScreen.append(div);
    jqScreen.append(name);

    jqScreen.append(divFrom);
    jqScreen.append(select);

    jqScreen.append(message);
    jqScreen.append(trigger);
}

function triggerCreateConversation() {
    var input = $('input.input-handle-name').val();

    // todo: validate the conversation before submit

    if (input.length > 0) {
        var data = {
            "my_handle": currentHandle,
            "guest_handle": input
        };
        $.ajax({
            type: "POST",
            url: conversationsApiUrl,
            data: data,
            success: function (data) {
                currentHandle = $(".handle-select").val();
                currentConversationId = data.conversation;
                sendNewMessage(data.conversation, $('.create-conversation-message').val(), loadChatScreen);
//                loadFirstChatData(data.conversation);
            }
        });
    } else {
        alert('nothing entered');
    }
}


// Helper

function isArrayEmpty(arr) {
    return (!(typeof arr !== 'undefined' && arr.length > 0));
}

// Function found here: http://stackoverflow.com/questions/4994201/is-object-empty
function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

// Function found here: http://stackoverflow.com/questions/4535888/jquery-text-and-newlines
function textEmbedNewlines(text) {
    var htmls = [];
    var lines = text.split(/\n/);
    // The temporary <div/> is to perform HTML entity encoding reliably.
    //
    // document.createElement() is *much* faster than jQuery('<div></div>')
    // http://stackoverflow.com/questions/268490/
    //
    // You don't need jQuery but then you need to struggle with browser
    // differences in innerText/textContent yourself
    var tmpDiv = jQuery(document.createElement('div'));
    for (var i = 0; i < lines.length; i++) {
        htmls.push(tmpDiv.text(lines[i]).html());
    }
    return htmls.join("<br>");
}