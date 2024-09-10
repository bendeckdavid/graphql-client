<?php

test('example', function () {

    var_dump(env('GRAPHQL_ENDPOINT'), config('graphqlclient.graphql_endpoint'));

    expect(true)->toBeTrue();
});
