<?php
header("content-type: text/javascript");

require (__DIR__.'/../../../libs/Lito/Apalabro/Loader-min.php');
?>

var strings = new Array();

strings['which_letter_use'] = '<?php echo str_replace("\'", '&apos;', __('Which letter use?')); ?>';
strings['play_tiles'] = '<?php echo str_replace("\'", '&apos;', __('Play this tiles?')); ?>';
strings['waiting_reply'] = '<?php echo str_replace("\'", '&apos;', __('Waiting for reply...')); ?>';
strings['no_results'] = '<?php echo str_replace("\'", '&apos;', __('No results for your query')); ?>';