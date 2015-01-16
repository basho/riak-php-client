<?php

namespace Basho\Riak\Core\Adapter\Rpb\Kv;

use Basho\Riak\ProtoBuf\RpbContent;
use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Message\Kv\Content;
use Basho\Riak\Core\Adapter\Rpb\RpbClient;

/**
 * Base rpb strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseRpbStrategy implements Strategy
{
    /**
     * @var \Basho\Riak\Core\Adapter\Rpb\RpbClient
     */
    protected $client;

    /**
     * @param \Basho\Riak\Core\Adapter\Rpb\RpbClient $client
     */
    public function __construct(RpbClient $client)
    {
        $this->client = $client;
    }

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
        foreach ($rpbcontent->getIndexes() as $index) {
            $key   = $index->getKey();
            $value = $index->getValue()->get();

            $content->indexes[$key] = $value;
        }

        /** @var $index \Basho\Riak\ProtoBuf\RpbPair */
        foreach ($rpbcontent->getUsermeta() as $meta) {
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
