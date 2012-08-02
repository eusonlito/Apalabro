$(document).ready(function () {
    $('.randomize').click(function () {
        var randomize = $($(this).data('randomize'));
        var divs = randomize.children('.tile-50');

        while (divs.length) {
            randomize.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
        }

        return false;
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
                var letter = prompt('Which letter use?');

                if (!/^[a-zA-Z\u00C0-\u00ff]+$/.test(letter)) {
                    alert('Letter not valid');

                    return true;
                }

                $('span.letter', $(this)).html(letter);
            }

            $(this).append('<input type="hidden" id="board-tile-' + position + '" name="letters[' + position + ']" value="' + $('span.letter', $(this)).html() + '" />');

            $(this).animate({
                top: snapToHight(this, dropped),
                left: snapToWidth(this, dropped)
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });

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

            ui.draggable.animate({
                top: snapToHight(ui.draggable, $(this))
            }, {
                duration: 600,
                easing: 'easeOutBack'
            });
        }
    });
});

function snapToHight (dragger, target) {
    return target.position().top - dragger.data('position').top + (target.outerHeight(true) - dragger.outerHeight(true)) / 2;
}

function snapToWidth (dragger, target) {
    return target.position().left - dragger.data('position').left + (target.outerWidth(true) - dragger.outerWidth(true)) / 2;
}