<?php

// Gerado pelo comando legacy:create:tests para a classe BaseTestName.
test('success response', function () {
    $this->get('ProjectRoute')->assertSuccessful();
});
