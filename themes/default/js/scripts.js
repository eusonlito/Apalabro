$(document).ready(function () {
    $('.randomize').click(function () {
        var randomize = $($(this).data('randomize'));
        var divs = randomize.children();

        while (divs.length) {
            randomize.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
        }

        return false;
    });

    $('.recall').click(function () {
        var rack = $('.rack-tiles');

        $('div', rack).each(function () {
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

    $('.filter-list').keyup(function (e) {
        if (e.keyCode == 27) {
            $(this).val('');
            $($(this).data('filtered')).show();

            return false;
        }

        var filter = $(this).val();
        var length = $(this).val().length;

        if (length > 0) {
            $($(this).data('filtered')).each(function () {
                var $this = $(this);

                if ($this.text().indexOf(filter) != -1) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
        } else {
            $($(this).data('filtered')).show();
        }
    }).keydown(function (e) {
        if (e.which == 13) {
            e.preventDefault();
        }  
    });

    $('.filter-expression').keyup(function (e) {
        var $filtered = $($(this).data('filtered'));

        if (e.keyCode == 27) {
            $(this).val('');
            $filtered.html('');

            return false;
        } else if (e.which != 13) {
            return false;
        }

        var filter = $(this).val();
        var length = $(this).val().length;

        if (length > 0) {
            $.post(BASE_WWW + 'search-words.php', {
                tiles: $(this).data('tiles'),
                filter: filter
            }, function (response) {
                console.log($(this).data('filtered'));
                $filtered.html(response);
            });
        } else {
            $filtered.html('');
        }
    });

    $('.rack-tiles div').draggable({
        cursor: "move",
        revert:  function (dropped) {
            if (!dropped) {
                return true;
            }

            if ($(dropped).hasClass('rack-tiles')) {
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
                $('#game-form button[type="submit"]').attr('disabled', false);
            } else {
                $('#game-form button[type="submit"]').attr('disabled', 'disabled');
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
                $('#game-form button[type="submit"]').attr('disabled', false);
            } else {
                $('#game-form button[type="submit"]').attr('disabled', 'disabled');
            }

            ui.draggable.animate({
                top: snapToHight(ui.draggable, $(this))
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });
        }
    });

    $('#game-form').submit(function () {
        if (!playReady()) {
            return false;
        }

        return confirm(strings['play_tiles']);
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

    var empty = new Array;

    $('table.board td[data-position!=""]').each(function () {
        empty[$(this).data('position')] = true;
    });

    if (empty.length == (15 * 15)) {
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