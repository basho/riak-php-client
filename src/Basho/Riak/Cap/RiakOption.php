<?php

namespace Basho\Riak\Cap;

/**
 * Encapsulates a riak command option.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakOption
{
    /**
     * Read Quorum.
     * How many replicas need to agree when fetching the object.
     */
    const R = 'r';

    /**
     * Primary Read Quorum.
     * How many primary replicas need to be available when retrieving the object.
     */
    const PR = 'pr';

    /**
     * Basic Quorum.
     * Whether to return early in some failure cases (eg. when r=1 and you get
     * 2 errors and a success basic_quorum=true would return an error)
     */
    const BASIC_QUORUM = 'basicQuorum';

    /**
     * Not Found OK.
     * Whether to treat notfounds as successful reads for the purposes of R
     */
    const NOTFOUND_OK = 'notfoundOk';

    /**
     * If Modified.
     * When a vector clock is supplied with this option, only return the object
     * if the vector clocks don't match.
     */
    const IF_MODIFIED = 'ifModified';

    /**
     * Head.
     * return the object with the value(s) set as empty. This allows you to get the
     * meta data without a potentially large value. Analogous to an HTTP HEAD request.
     */
    const HEAD = 'head';

    /**
     * Deleted VClock.
     * By default single tombstones are not returned by a fetch operations. This
     * will return a Tombstone if it is present.
     */
    const DELETED_VCLOCK = 'deletedvclock';

    /**
     * Timeout.
     * Sets the server-side timeout for this operation. The default in Riak is 60 seconds.
     */
    const TIMEOUT = 'timeout';

    /**
     * Sloppy quorum.
     */
    const SLOPPY_QUORUM = 'sloppyQuorum';

    /**
     * N value.
     */
    const N_VAL = 'nVal';

    /**
     * Write Quorum.
     * How many replicas to write to before returning a successful response.
     */
    const W = 'w';

    /**
     * Durable Write Quorum.
     * How many replicas to commit to durable storage before returning a successful response.
     */
    const DW = 'dw';

    /**
     * Primary Write Quorum.
     * How many primary nodes must be up when the write is attempted.
     */
    const PW = 'pw';

    /**
     * If Not Modified.
     * Update the value only if the vclock in the supplied object matches the one in the database.
     */
    const IF_NOT_MODIFIED = 'ifNotModified';

    /**
     * If None Match.
     * Store the value only if this bucket/key combination are not already defined.
     */
    const IF_NONE_MATCH = 'ifNoneMatch';

    /**
     * Return Body.
     * Return the object stored in Riak. Note this will return all siblings.
     */
    const RETURN_BODY = 'returnBody';

    /**
     * Return Head.
     * Like {@link #RETURN_BODY} except that the value(s) in the object are blank to
     * avoid returning potentially large value(s).
     */
    const RETURN_HEAD = 'returnHead';

    /**
     * ASIS
     */
    const ASIS = 'asis';
}
