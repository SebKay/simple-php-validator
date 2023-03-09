<?php

use SebKay\SPV\Validator;

it("validates 'required' fields", function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'email' fields", function () {
    $errors = Validator::validate([
        'email' => 'test',
    ], [
        'email' => ['email'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'password' fields", function () {
    $errors = Validator::validate([
        'password' => '1234',
    ], [
        'password' => ['password'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'same' fields", function () {
    $errors = Validator::validate([
        'password' => '12345',
        'password_confirmation' => '123456',
    ], [
        'password_confirmation' => ['same:password'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("skips 'nullable' fields", function () {
    $errors = Validator::validate([
        'name' => '',
        'email' => 'test@test.com',
    ], [
        'name' => ['nullable'],
        'email' => ['required'],
    ]);

    expect($errors)->toHaveCount(0);
});

it('throws errors as exceptions', function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    Validator::throwErrors($errors);
})->throws(\InvalidArgumentException::class);

it("doesn't throw exceptions when there are no errors", function () {
    $errors = Validator::validate([
        'name' => 'Jim Gordon',
    ], [
        'name' => ['required'],
    ]);

    expect($errors)->toHaveCount(0);

    Validator::throwErrors($errors);
});
