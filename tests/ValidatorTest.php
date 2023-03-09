<?php

use SebKay\SPV\Validator;

it("validates 'required' errors", function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'email' errors", function () {
    $errors = Validator::validate([
        'email' => 'test',
    ], [
        'email' => ['email'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'password' errors", function () {
    $errors = Validator::validate([
        'password' => '1234',
    ], [
        'password' => ['password'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'same' errors", function () {
    $errors = Validator::validate([
        'password' => '12345',
        'password_confirmation' => '123456',
    ], [
        'password_confirmation' => ['same:password'],
    ]);

    expect($errors)->toHaveCount(1);
});
