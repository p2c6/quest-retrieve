<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PostStatus extends Enum
{
    const PENDING  = "Pending";
    const ON_PROCESSING = "On Processing";
    const FINISHED = "Finished";
    const DEACTIVATED = "Deactivated";
    const REJECT = "Reject";
}
