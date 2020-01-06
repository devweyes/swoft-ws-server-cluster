<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Jcsp\WsCluster\Annotation\Mapping\Cluster;

/**
 * Class ClusterParser
 *
 * @AnnotationParser(Cluster::class)
 * @since 2.0
 * @package Jcsp\WsCluster\Annotation\Parser
 */
class ClusterParser extends Parser
{
    /**
     * @param int $type
     * @param CacheClear $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
    	//TODO 识别注册机器 生成机器唯一name
    	//需注册在类上
        if ($type === self::TYPE_CLASS) {

        }

        return [];
    }
}
