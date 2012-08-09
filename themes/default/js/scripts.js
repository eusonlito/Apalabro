$(document).ready(function () {
    $('.randomize').click(function () {
        var randomize = $($(this).data('randomize'));
        var divs = randomize.children();

        while (divs.length) {
            randomize.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
        }

        return false;
    });

    $('a[data-action="recall"]').click(function () {
        var rack = $('.rack-tiles');

        $('a[data-action="confirm"]').attr('disabled', 'disabled');

        $('div', rack).each(function () {
            $('input', this).remove();

            $(this).animate({
                top: snapToHight($(this), $(rack)),
                left: (rack.position().left + $(this).data('position').left)
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });
        });

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

        if ($('input[type="text"]', $(this)).val().length > 0) {
            $filtered.html('<li><h4>' + strings['waiting_reply'] + '</h4></li>');

            $.post($(this).attr('action'),
                $('input', $(this)).serialize(),
                function (response) {
                    $filtered.html(response);
                }
            );
        } else {
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

                if (!/^[a-zA-Z\u00C0-\u00ff]+$/.test(letter)) {
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
            $('#confirm-move button').remove();
        } else {
            $('#game-form button[name="play"]').attr('disabled', 'disabled');
        }

        $('#confirm-move .modal-body').html('<h4>' + strings['waiting_reply'] + '</h4>');

        $.post($(this).data('url'),
            $('#game-form input').serialize(),
            function (response) {
                response = $.parseJSON(response);

                $('#confirm-move .modal-body').html(response.html);

                if (!test && !response.error) {
                    $('#game-form button[name="play"]').attr('disabled', false);
                }
            }
        );

        $('#confirm-move').modal();

        return false;
    });

    $('a[data-action="swap"]').click(function () {
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

        $('div.swap').hide();
        $('table.board').show();

        return false;
    });
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