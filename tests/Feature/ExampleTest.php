<?php

test('la raíz redirige al dashboard', function () {
    $this->get('/')->assertRedirect('/dashboard');
});
