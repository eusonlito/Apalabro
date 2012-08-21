var chat = new Array();

chat['id'] = '';
chat['time'] = 0;
chat['base'] = 10000;
chat['limit'] = chat['base'] * 10;
chat['interval'] = 0;
chat['url'] = BASE_WWW+'ajax/chat.php'+window.location.search+'&last=';

function updateChat (restart) {
    var updated = restart;

    if ((updated == true) || (chat['time'] > 0)) {
        $.post(chat['url']+chat['id'], function (response) {
            if ((response == null) || !response.id || !response.html || !response.new) {
                return false;
            }

            if (chat['id'] == response.id) {
                return true;
            }

            chat['id'] = response.id;
            updated = true;

            var $layer = $('#modal-chat .modal-body');
            var $link = $('a.chat-24');

            if ($link.text() != '') {
                $link.text(parseInt($link.text()) + response.new);
            } else {
                $link.text(response.new);
            }

            if ($layer.is(':visible')) {
                if ($('.alert-empty', $layer).length) {
                    $('.alert-empty', $layer).remove();
                }

                $layer.append(response.html);
                $layer.scrollTop($layer[0].scrollHeight);
            } else {
                $link.attr('title', strings['new_messages']);
                $link.css('color', '#B94A48');

                if (document.title.match(/^\([0-9]+\)/)) {
                    document.title = document.title.replace(/^\([0-9]+\)/, '('+response.new+')');
                } else {
                    document.title = '('+response.new+') '+document.title;
                }
            }
        });
    }

    var previous = chat['time'];

    if (updated == true) {
        chat['time'] = chat['base'];
    } else {
        chat['time'] = chat['time'] + chat['base'];
        chat['time'] = (chat['time'] > chat['limit']) ? chat['limit'] : chat['time'];
    }

    if (previous != chat['time']) {
        clearInterval(chat['interval']);

        chat['interval'] = setInterval('updateChat(false)', chat['time']);
    }
}

$(document).ready(function () {
    $('a.chat-24').click(function () {
        var $layer = $('#modal-chat');

        $('.modal-body', $layer).html('<div class="center"><img src="'+BASE_THEME+'images/loading.gif" /></div>');

        $.post(chat['url'],
            function (response) {
                $('.modal-body', $layer).html(response.html ? response.html : strings['server_error']);
                $('.modal-body', $layer).scrollTop( $('.modal-body', $layer)[0].scrollHeight);
            }
        );

        $layer.modal();

        document.title = document.title.replace(/^\([0-9]+\) /, '');

        $(this).attr('title', strings['your_messages'].replace(/%s/, $(this).text()));
        $(this).css('color', '');

        return false;
    });

    $('#modal-chat form').submit(function () {
        var $text = $('input[type="text"]', $(this));

        $text.val($.trim($text.val()));

        if ($text.val().lenght == 0) {
            return false;
        }

        var $submit = $('button[type="submit"]', $(this));

        $submit.attr('disabled', 'disabled');

        $.post(chat['url']+chat['id'], $(this).serialize(), function () {
            updateChat(true);

            $submit.attr('disabled', false);
        });

        $text.attr('value', '');

        return false;
    });

    updateChat(false);
});