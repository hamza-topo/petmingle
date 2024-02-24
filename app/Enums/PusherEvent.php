<?php

namespace App\Enums;

enum PusherEvent
{
    const IS_WRITING_TO = 'is-writing-to';
    const ITS_A_NEW_MATCH = 'its-a-new-match';
    const ITS_A_NEW_MESSAGE = 'its-a-new-message';
}
