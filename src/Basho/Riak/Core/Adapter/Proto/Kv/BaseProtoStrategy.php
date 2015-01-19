<?php

namespace Basho\Riak\Core\Adapter\Proto\Kv;

use Basho\Riak\ProtoBuf\RpbContent;
use Basho\Riak\Core\Message\Kv\Content;
use Basho\Riak\Core\Adapter\Proto\ProtoStrategy;

/**
 * Base rpb strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseProtoStrategy extends ProtoStrategy
{
    /**
     * @param \Basho\Riak\ProtoBuf\RpbContent $rpbcontent
     *
     * @return \Basho\Riak\Core\Message\Kv\Content
     */
    private function createContent(RpbContent $rpbcontent)
    {
        $content = new Content();

        $content->contentType  = $rpbcontent->getContentType()->get();
        $content->lastModified = $rpbcontent->getLastMod()->get();
        $content->vtag         = $rpbcontent->getVtag()->get();
        $content->value        = $rpbcontent->getValue();
        $content->indexes      = [];
        $content->metas        = [];

        /** @var $index \Basho\Riak\ProtoBuf\RpbPair */
        foreach ($rpbcontent->indexes as $index) {
            $key   = $index->getKey();
            $value = $index->getValue()->get();

            $content->indexes[$key][] = $value;
        }

        /** @var $index \Basho\Riak\ProtoBuf\RpbPair */
        foreach ($rpbcontent->usermeta as $meta) {
            $key   = $meta->getKey();
            $value = $meta->getValue()->get();

            $content->metas[$key] = $value;
        }

        return $content;
    }

    /**
     * @param \Basho\Riak\ProtoBuf\RpbContent[] $contentList
     *
     * @return \Basho\Riak\Core\Message\Kv\Content[]
     */
    protected function createContentList(array $contentList)
    {
        return array_map([$this, 'createContent'], $contentList);
    }
}
