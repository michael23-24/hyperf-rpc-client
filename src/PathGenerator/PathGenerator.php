<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient\PathGenerator;

use MicroTool\HyperfRpcClient\Traits\Str;

class PathGenerator implements PathGeneratorInterface
{
    use Str;

    public function generate($service, $method)
    {
        $handledNamespace = explode('\\', $service);
        $handledNamespace = $this->replaceArray('\\', ['/'], end($handledNamespace));
        $handledNamespace = $this->replaceLast('Service', '', $handledNamespace);
        $path             = $this->snake($handledNamespace);

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        return $path . '/' . $method;
    }


}
