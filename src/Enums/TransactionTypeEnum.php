<?php

namespace Aldeebhasan\LaSubscription\Enums;

enum TransactionTypeEnum: string
{
    case NEW = "new";
    case RENEW = "renew";
    case CANCEL = "cancel";
}
