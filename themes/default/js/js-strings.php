<?php
header("content-type: text/javascript");

require (__DIR__.'/../../../libs/Lito/Apalabro/Loader-min.php');
?>

var strings = new Array();

strings['which_letter_use'] = '<?php echo str_replace("\'", '&apos;', __('Which letter use?')); ?>';
strings['play_tiles'] = '<?php echo str_replace("\'", '&apos;', __('Play this tiles?')); ?>';
strings['waiting_reply'] = '<?php echo str_replace("\'", '&apos;', __('Waiting for reply...')); ?>';
strings['no_results'] = '<?php echo str_replace("\'", '&apos;', __('No results for your query')); ?>';
strings['swap_tiles'] = '<?php echo str_replace("\'", '&apos;', __('Are you sure do you want to swap this tiles?')); ?>';
strings['no_swap_tiles'] = '<?php echo str_replace("\'", '&apos;', __('You must select some tile to swap')); ?>';
strings['resign'] = '<?php echo str_replace("\'", '&apos;', __('Are you sure that you want resign this game?!?')); ?>';
strings['resign_sure'] = '<?php echo str_replace("\'", '&apos;', __('100% Sure?')); ?>';
strings['pass'] = '<?php echo str_replace("\'", '&apos;', __('Are you sure that you want pass this turn?')); ?>';