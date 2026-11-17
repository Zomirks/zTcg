<?php

use App\Enums\Language;
use App\Enums\ProductType;

test('product type options map each case to its label', function () {
    expect(ProductType::getOptions())
        ->toContain(['label' => 'ETB', 'value' => 'etb']);
});

test('language options map each case to its label', function () {
    expect(Language::getOptions())
        ->toContain(['label' => 'Français', 'value' => 'fr']);
});
