<?php

use App\Services\Reports\RegistrationService;

describe('RegistrationService::frequencyTotal', function () {
    test('general absence with zero absence', function () {
        $result = RegistrationService::frequencyTotal(true, 0, 1.0, 100.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('general absence with all days absent', function () {
        $result = RegistrationService::frequencyTotal(true, 20, 1.0, 100.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(0.0);
    });

    test('non-general absence normal calculation', function () {
        $result = RegistrationService::frequencyTotal(false, 10, 0.5, 100.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(95.0);
    });

    test('non-general absence with zero absence total', function () {
        $result = RegistrationService::frequencyTotal(false, 0, 0.5, 100.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('non-general absence with empty grade workload', function () {
        $result = RegistrationService::frequencyTotal(false, 10, 0.5, 0.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('non-general absence with zero course hour absence', function () {
        $result = RegistrationService::frequencyTotal(false, 10, 0.0, 100.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('truncation test - should not round up', function () {
        $result = RegistrationService::frequencyTotal(false, 1, 0.833333333333333, 20.0, 20);
        expect($result)->toBeFloat();
        expect($result)->toBe(95.8); // Should truncate, not round
    });
});

describe('RegistrationService::frequencyByDiscipline', function () {
    test('zero absence', function () {
        $result = RegistrationService::frequencyByDiscipline(0, 0.5, 100.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('empty discipline workload', function () {
        $result = RegistrationService::frequencyByDiscipline(5, 0.5, 0.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('zero hour absence', function () {
        $result = RegistrationService::frequencyByDiscipline(5, 0.0, 100.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });

    test('normal calculation with positive result', function () {
        $result = RegistrationService::frequencyByDiscipline(5, 0.5, 100.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(97.5);
    });

    test('floating point precision test', function () {
        $result = RegistrationService::frequencyByDiscipline(24, 0.833333333333333, 20.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(0.0);
    });

    test('negative result should be handled', function () {
        $result = RegistrationService::frequencyByDiscipline(30, 1.0, 20.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(-50.0);
    });

    test('truncation test - should not round up', function () {
        $result = RegistrationService::frequencyByDiscipline(1, 0.833333333333333, 20.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(95.8); // Should truncate, not round
    });

    test('very small result should be handled', function () {
        $result = RegistrationService::frequencyByDiscipline(1, 0.000000000001, 1000000.0);
        expect($result)->toBeFloat();
        expect($result)->toBe(100.0);
    });
});
