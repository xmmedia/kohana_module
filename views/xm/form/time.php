<?php echo $fields['hour']; ?>&nbsp;:&nbsp;<?php echo $fields['min']; ?><?php if ($options['show_seconds']) { ?>&nbsp;:&nbsp;<?php } // if ?><?php echo $fields['sec']; ?><?php if ( ! $options['24_hour']) { ?>&nbsp;<?php echo $fields['am_pm']; } // if ?>