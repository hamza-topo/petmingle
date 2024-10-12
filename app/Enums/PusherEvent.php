<?php

namespace App\Enums;

enum PusherEvent
{
    /**
     * consts are used for channel pusher
     * @author Hamza ait sidi said <hamzaaitsidisaid.11@gmail.com>
     */
    const IS_WRITING_TO = 'is-writing-to';

    const ITS_A_NEW_MATCH = 'its-a-new-match';

    const ITS_A_NEW_MESSAGE = 'its-a-new-message';
    
    const ITS_NEW_ADOPTION = 'its-a-new-adoption';

}
