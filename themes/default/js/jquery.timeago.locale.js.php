<?php
header("content-type: text/javascript");

require (__DIR__.'/../../../libs/Lito/Apalabro/Loader-min.php');
?>

jQuery.timeago.settings.strings = {
  prefixAgo: '<?php echo (__('prefixAgo') === 'prefixAgo') ? '' : str_replace("\'", '&apos;', __('prefixAgo')); ?>',
  prefixFromNow: '<?php echo (__('prefixFromNow') === 'prefixFromNow') ? '' : str_replace("\'", '&apos;', __('prefixFromNow')); ?>',
  suffixAgo: '<?php echo (__('suffixAgo') === 'suffixAgo') ? '' : str_replace("\'", '&apos;', __('suffixAgo')); ?>',
  suffixFromNow: '<?php echo (__('suffixFromNow') === 'suffixFromNow') ? '' : str_replace("\'", '&apos;', __('suffixFromNow')); ?>',
  seconds: '<?php echo str_replace("\'", '&apos;', __('less than a minute')); ?>',
  minute: '<?php echo str_replace("\'", '&apos;', __('about a minute')); ?>',
  minutes: '<?php echo str_replace("\'", '&apos;', __('%d minutes')); ?>',
  hour: '<?php echo str_replace("\'", '&apos;', __('about an hour')); ?>',
  hours: '<?php echo str_replace("\'", '&apos;', __('about %d hours')); ?>',
  day: '<?php echo str_replace("\'", '&apos;', __('a day')); ?>',
  days: '<?php echo str_replace("\'", '&apos;', __('%d days')); ?>',
  month: '<?php echo str_replace("\'", '&apos;', __('about a month')); ?>',
  months: '<?php echo str_replace("\'", '&apos;', __('%d months')); ?>',
  year: '<?php echo str_replace("\'", '&apos;', __('about a year')); ?>',
  years: '<?php echo str_replace("\'", '&apos;', __('%d years')); ?>',
  wordSeparator: ' ',
  numbers: []
};
