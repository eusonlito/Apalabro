var chat = new Array();

chat['id'] = '';
chat['url'] = BASE_WWW + 'ajax/chat.php' + window.location.search + '&last=';

function updateChat(post) {
    'use strict';

    var $layer = $('#modal-chat .modal-body');
    var $link = $('a.chat-24');

    if (!$layer.is(':visible')) {
        $link.attr('title', strings['new_messages']);
        $link.css('color', '#B94A48');

        return true;
    }

    var $submit = $('#modal-chat button[type="submit"]');

    $submit.attr('disabled', 'disabled');

    $.post(chat['url'] + chat['id'], post, function (response) {
        $submit.attr('disabled', false);

        if ((response === null) || !response.html) {
            return false;
        }

        if (!response.id && response.html) {
            $layer.html(response.html);
            return false;
        }

        if (chat['id'] === response.id) {
            return true;
        }

        if ($('.alert-empty', $layer).length) {
            $('.alert-empty', $layer).remove();
        }

        var updates = parseInt(response.new);

        if (chat['id']) {
            $layer.append(response.html);

            if ($link.text() !== '') {
                $link.text(parseInt($link.text()) + updates);
            } else {
                $link.text(updates);
            }
        } else {
            $layer.html(response.html);
        }

        $layer.scrollTop($layer[0].scrollHeight);

        $link.attr('title', strings['your_messages'].replace(/%s/, updates));
        $link.css('color', '');

        chat['id'] = response.id;
    });

    return true;
}

$(document).ready(function () {
    'use strict';

    $('a.chat-24').click(function () {
        var $layer = $('#modal-chat');

        if (!chat['id']) {
            $('.modal-body', $layer).html('<div class="center"><img src="' + BASE_THEME + 'images/loading.gif" /></div>');
        }

        $layer.modal();

        updateChat();

        $(this).attr('title', strings['your_messages'].replace(/%s/, $(this).text()));
        $(this).css('color', '');

        return false;
    });

    $('#modal-chat form').submit(function () {
        var $text = $('input[type="text"]', $(this));

        $text.val($.trim($text.val()));

        if ($text.val().lenght === 0) {
            return false;
        }

        updateChat($(this).serialize());

        $text.attr('value', '');

        return false;
    });
});
