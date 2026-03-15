<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Wiki Builder API')
        ->assertSee('/api/v1', false);
});
