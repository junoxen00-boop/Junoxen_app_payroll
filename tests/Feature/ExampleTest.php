<?php

test('root redirects to login', function () {
    $this->get('/')->assertRedirect(route('login'));
});
