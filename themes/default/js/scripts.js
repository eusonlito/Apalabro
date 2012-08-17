$(document).ready(function () {
    $('span[class="letter"]').each(function () {
        var letters = $(this).text().length;

        if (letters == 1) {
            return true;
        }

        $(this).css('font-size', parseInt($(this).css('font-size')) - 7);
    });
    
    $('#new-game').on('click', '.well.user', function () {
        $('input[name="user_id"]', $('#new-game')).val($(this).data('id'));

        $('#new-game .well.user').css('background-color', 'whiteSmoke');
        $(this).css('background-color', 'yellowGreen');

        $('button[type="submit"]', $(this).parents('div.tab-pane')).attr('disabled', false);
    });

    $('.filter-users button[type="submit"]').click(function (e) {
        if (($(this).data('url') == undefined) || ($(this).data('filtered') == undefined)) {
            return true;
        }

        e.preventDefault(); 

        var $filtered = $($(this).data('filtered'));

        if ($('input[type="text"][name="search"]').val().length > 0) {
            $filtered.html('<div class="span12 center"><img src="'+BASE_THEME+'images/loading.gif" /></div>');

            $.post($(this).data('url'), {
                filter: $('input[type="text"][name="search"]').val(),
                }, function (response) {
                    $filtered.html(response);
                }
            );
        }

        return false;
    }).keydown(function (e) {
        if (e.which == 13) {
            e.preventDefault();
        }

        return false;
    });

    $('.randomize').click(function () {
        var randomize = $($(this).data('randomize'));
        var divs = randomize.children();

        while (divs.length) {
            randomize.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
        }

        return false;
    });

    $('a[data-action="recall"]').click(function () {
        $('a[data-action="confirm"]').attr('disabled', 'disabled');

        recall();

        return false;
    });

    $('#suggestions-previous button').click(function () {
        $('#suggestions-previous').remove();
        $('#suggestions').show();

        return false;
    });

    $('.filter-list').keyup(function (e) {
        if (e.keyCode == 27) {
            $(this).val('');
            $($(this).data('filtered')).parent('div').show();

            return false;
        }

        var filter = $(this).val();
        var length = $(this).val().length;

        if (length > 0) {
            $($(this).data('filtered')).each(function () {
                var $this = $(this);

                if ($this.text().indexOf(filter) != -1) {
                    $this.parent('div').show();
                } else {
                    $this.parent('div').hide();
                }
            });
        } else {
            $($(this).data('filtered')).parent('div').show();
        }
    }).keydown(function (e) {
        if (e.which == 13) {
            e.preventDefault();
        }  
    });

    $('.filter-expression').submit(function (e) {
        e.preventDefault(); 

        var $filtered = $($(this).data('filtered'));
        var $button = $('button[type="submit"]', $(this));

        $button.attr('disabled', 'disabled');

        if ($('input[type="text"]', $(this)).val().length > 0) {
            $filtered.html('<li class="center"><img src="'+BASE_THEME+'images/loading.gif" class="mt-20" /></li>');

            $.post($(this).attr('action'),
                $('input', $(this)).serialize(),
                function (response) {
                    $button.attr('disabled', false);
                    $filtered.html(response);
                }
            );
        } else {
            $button.attr('disabled', false);
            $filtered.html('');
        }

        return false;
    });

    $('.rack-tiles div').draggable({
        cursor: "move",
        revert:  function (dropped) {
            if (!dropped) {
                return true;
            }

            if ($(dropped).hasClass('rack-tiles') || $(dropped).hasClass('droppable-swap')) {
                return false;
            }

            var position = $(dropped).data('position');

            if ($('#board-tile-' + position).length > 0) {
                return true;
            }

            if ($(this).hasClass('wildcard')) {
                var letter = prompt(strings['which_letter_use']);

                if ((letter == null) || (letter == '')) {
                    return true;
                }

                if ($.inArray(letter, VALID_LETTERS) == -1) {
                    alert('Letter not valid');
                    return true;
                }

                $('span.letter', $(this)).html(letter);

                letter = '-' + letter;
            } else {
                var letter = $('span.letter', $(this)).html();
            }

            $(this).append('<input type="hidden" id="board-tile-' + position + '" name="played_tiles[' + position + ']" value="' + letter + '" />');

            $(this).animate({
                top: snapToHight(this, dropped),
                left: snapToWidth(this, dropped)
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });

            if (playReady()) {
                $('a[data-action="confirm"]').attr('disabled', false);
                $('a[data-action="test"]').attr('disabled', false);
            } else {
                $('a[data-action="confirm"]').attr('disabled', 'disabled');
                $('a[data-action="test"]').attr('disabled', 'disabled');
            }

            return false;
        },
        opacity: 0.8,
        cursorAt: {
            left: 15
        },
        start: function () {
            $('input', this).remove();
            $(this).stop(true, true);
        },
        create: function () {
            $(this).data('position', $(this).position());
        }
    });

    $('.board td:not(.tile-35)').droppable({
        accept: ".rack-tiles > div"
    });

    $('.rack-tiles').droppable({
        hoverClass: 'hover',
        drop: function (event, ui) {
            if (ui.draggable.hasClass('wildcard')) {
                $('span.letter', $(ui.draggable)).html('*');
            }

            if (playReady()) {
                $('a[data-action="confirm"]').attr('disabled', false);
                $('a[data-action="test"]').attr('disabled', false);
            } else {
                $('a[data-action="confirm"]').attr('disabled', 'disabled');
                $('a[data-action="test"]').attr('disabled', 'disabled');
            }

            ui.draggable.animate({
                top: snapToHight(ui.draggable, $(this))
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });
        }
    });

    $('#game-form button[type="submit"]').click(function () {
        $('#game-form').data('clicked', $(this).attr('name'));
    });

    $('#game-form').submit(function () {
        var clicked = $(this).data('clicked');

        switch (clicked) {
            case 'play':
                if (!playReady()) {
                    return false;
                }

                $('#modal-confirm .modal-header').hide();
                $('#modal-confirm .modal-footer').hide();

                $('#modal-confirm .modal-body').html(
                    '<h2 class="center"><img src="'+BASE_THEME+'images/loading.gif" />'+
                    '<span class="offset05">'+strings['sending']+'<span></h2>'
                );

                return true;

            case 'swap':
                if ($('input[name^=swapped_tiles\\[]').length == 0) {
                    alert(strings['no_swap_tiles']);
                    return false;
                }

                return confirm(strings['swap_tiles']);

            case 'pass':
                return confirm(strings['pass']);

            case 'resign':
                if (!confirm(strings['resign'])) {
                    return false;
                }
                
                return confirm(strings['resign_sure']);
        }

        return false;
    });

    $('a[data-action="confirm"], a[data-action="test"]').click(function () {
        if (!playReady()) {
            return false;
        }

        var test = ($(this).data('action') == 'test') ? true : false;

        if (test) {
            $('#modal-confirm button').remove();
        } else {
            $('#game-form button[name="play"]').attr('disabled', 'disabled');
        }

        $('#modal-confirm .modal-body').html('<div class="center"><img src="'+BASE_THEME+'images/loading.gif" /></div>');

        $.post($(this).data('url'),
            $('#game-form input').serialize(),
            function (response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {
                    $('#modal-confirm .modal-body').html(strings['server_error']);
                    return false;
                }

                $('#modal-confirm .modal-body').html(response.html);

                if (!test && !response.error) {
                    $('#game-form button[name="play"]').attr('disabled', false);
                }
            }
        );

        $('#modal-confirm').modal({
            keyboard: false,
            backdrop: "static"
        });

        return false;
    });

    $('a[data-action="swap"]').click(function () {
        recall();

        $('table.board').hide();
        $('div.swap').show();

        return false;
    });

    $('.droppable-swap').droppable({
        hoverClass: 'hover',
        drop: function (event, ui) {
            $(this).append('<input type="hidden" name="swapped_tiles[]" value="' + $('span.letter', $(ui.draggable)).html() + '" />');

            ui.draggable.animate({
                top: snapToHight(ui.draggable, $(this))
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });
        }
    });

    $('.swap button.btn-danger').click(function () {
        $('input[name^=swapped_tiles\\[]').remove();

        recall();

        $('div.swap').hide();
        $('table.board').show();

        return false;
    });

    $('a.chat-24').click(function () {
        $('#modal-chat .modal-body').html('<div class="center"><img src="'+BASE_THEME+'images/loading.gif" /></div>');

        $.post($(this).attr('href'),
            function (response) {
                $('#modal-chat .modal-body').html(response);
            }
        );

        $('#modal-chat').modal();

        return false;
    });

    if ((typeof(UPDATED) != 'undefined') && (UPDATED != '')) {
        var just_updated = new Array();

        var pushInterval = setInterval(function () {
            $.ajax({
                type: 'POST',
                data: 'u='+UPDATED,
                url: BASE_WWW+'ajax/push.php',
                success: function (response) {
                    try {
                        response = $.parseJSON(response);
                    } catch (e) {
                        return false;
                    }

                    if (response.error == true) {
                        clearInterval(pushInterval);
                        return false;
                    } else if ((response.length == 0) || (response.length == just_updated.length)) {
                        return true;
                    }

                    if (document.title.match(/^\([0-9]+\)/)) {
                        document.title = document.title.replace(/^\([0-9]+\)/, '('+response.length+')');
                    } else {
                        document.title = '('+response.length+') '+document.title;
                    }

                    $updates = $('div.navbar li#updates');

                    if ($updates.length == 0) {
                        $('div.navbar div.nav-collapse ul.pull-left').append(
                            '<li id="updates" class="dropdown">'+
                            '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'+
                            '<span>'+strings['updates']+'</span> <b class="caret"></b></a>'+
                            '<ul class="dropdown-menu"></ul>'+
                            '</li>'
                        );
                    }

                    $('#updates a span').text(response.length+' '+strings['updates']);

                    for (i = 0; i < response.length; i++) {
                        if ($.inArray(response[i].id, just_updated) == -1) {
                            $('#updates ul').prepend(
                                '<li id="updated-'+response[i].id+'"><a href="'+response[i].link+'">'+
                                '<strong>'+response[i].text+'</strong>'+
                                '</li>'
                            );

                            just_updated.push(response[i].id);
                        }
                    }
                }
            });
        }, 30000);
    }
});

function snapToHight (dragger, target) {
    return target.position().top - dragger.data('position').top + (target.outerHeight(true) - dragger.outerHeight(true)) / 2;
}

function snapToWidth (dragger, target) {
    return target.position().left - dragger.data('position').left + (target.outerWidth(true) - dragger.outerWidth(true)) / 2;
}

function nearWord (cell, empty) {
    if ((typeof(empty[cell + 1]) == 'undefined') || (typeof(empty[cell - 1]) == 'undefined')
    || (((cell + 15) < (15 * 15)) && (typeof(empty[cell + 15]) == 'undefined'))
    || (((cell - 15) >= 0) && (typeof(empty[cell - 15]) == 'undefined'))) {
        return true;
    } else {
        return false;
    }
}

function playReady () {
    var played_tiles = $('input[name^=played_tiles\\[]');
    var len = played_tiles.length;

    if (len == 0) {
        return false;
    }

    var total = 0;
    var empty = new Array;

    $('table.board td').each(function () {
        if ($(this).data('position') != undefined) {
            empty[$(this).data('position')] = true;
            total++;
        }
    });

    if (total == (15 * 15)) {
        return (len > 1) ? true : false;
    }

    var valid = false;
    var letter = '';
    var position = 0;

    for (i = 0; i < len; i++) {
        letter = played_tiles[i];
        cell = parseInt($(letter).attr('name').match(/[0-9]+/));

        if (nearWord(cell, empty)) {
            valid = true;
            break;
        }
    }

    return valid;
}

function recall () {
    var rack = $('.rack-tiles');

    $('div', rack).each(function () {
        $('input', $(this)).remove();

        if ($(this).hasClass('wildcard')) {
            $('span.letter', $(this)).html('*');
        }

        $(this).animate({
            top: snapToHight($(this), $(rack)),
            left: (rack.position().left + $(this).data('position').left)
        }, {
            duration: 600,
            easing: 'easeOutBack'
        });
    });
}