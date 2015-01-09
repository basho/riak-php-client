#!/bin/bash

sudo riak-admin bucket-type create counters '{"props":{"datatype":"counter"}}'
sudo riak-admin bucket-type create maps '{"props":{"datatype":"map"}}'
sudo riak-admin bucket-type create sets '{"props":{"datatype":"set"}}'
sudo riak-admin bucket-type activate counters
sudo riak-admin bucket-type activate maps
sudo riak-admin bucket-type activate sets
