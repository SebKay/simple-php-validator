<?php

use SebKay\SPV\Exceptions\ValidationException;
use SebKay\SPV\Validator;

it("validates 'required' fields", function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'required_with' fields", function () {
    $errors = Validator::validate([
        'name_1' => 'Jim',
        'name_2' => '',
    ], [
        'name_2' => ['required_with:name_1'],
    ]);

    expect($errors)->toHaveCount(1);

    $errors = Validator::validate([
        'name_1' => 'Jim',
        'name_2' => '',
    ], [
        'name_2' => ['required_with:name_1,Jim'],
    ]);

    expect($errors)->toHaveCount(1);

    $errors = Validator::validate([
        'name_1' => 'Jim',
        'name_2' => 'Bob',
    ], [
        'name_2' => ['required_with:name_1,Jim'],
    ]);

    expect($errors)->toHaveCount(0);

    $errors = Validator::validate([
        'name_1' => 'Bob',
        'name_2' => '',
    ], [
        'name_2' => ['required_with:name_1,Jim'],
    ]);

    expect($errors)->toHaveCount(0);
});

it("validates 'required_without' fields", function () {
    $errors = Validator::validate([
        'name_1' => '',
        'name_2' => '',
    ], [
        'name_2' => ['required_without:name_1'],
    ]);

    expect($errors)->toHaveCount(1);

    $errors = Validator::validate([
        'name_1' => 'Jim',
        'name_2' => '',
    ], [
        'name_2' => ['required_without:name_1'],
    ]);

    expect($errors)->toHaveCount(0);
});

it("validates 'email' fields", function () {
    $errors = Validator::validate([
        'email' => 'test',
    ], [
        'email' => ['email'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'accepted' fields", function () {
    $errors = Validator::validate([
        'terms' => 'no',
    ], [
        'terms' => ['accepted'],
    ]);

    expect($errors)->toHaveCount(1);

    $errors = Validator::validate([
        'terms' => 'yes',
    ], [
        'terms' => ['accepted'],
    ]);

    expect($errors)->toHaveCount(0);

    $errors = Validator::validate([
        'terms' => true,
    ], [
        'terms' => ['accepted'],
    ]);

    expect($errors)->toHaveCount(0);

    $errors = Validator::validate([
        'terms' => 1,
    ], [
        'terms' => ['accepted'],
    ]);

    expect($errors)->toHaveCount(0);
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

it("validates 'min' fields", function () {
    $errors = Validator::validate([
        'password' => '123',
    ], [
        'password' => ['min:4'],
    ]);

    expect($errors)->toHaveCount(1);
});

it("validates 'max' fields", function () {
    $errors = Validator::validate([
        'password' => '123456',
    ], [
        'password' => ['max:4'],
    ]);

    expect($errors)->toHaveCount(1);
    expect($errors['password']['max:4'])->toBe('Password must be at most 4 characters.');
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

it("validates 'strong_password' fields", function () {
    $errors = Validator::validate([
        'password' => 'test',
    ], [
        'password' => ['strong_password'],
    ]);

    expect($errors)->toHaveCount(1);
    expect($errors['password']['strong_password'])->toBe('Password must contain at least one uppercase letter, one lowercase letter, and one number.');

    $errors = Validator::validate([
        'password' => 'TEST',
    ], [
        'password' => ['strong_password'],
    ]);

    expect($errors)->toHaveCount(1);
    expect($errors)->toHaveKey('password');
    expect($errors['password']['strong_password'])->toBe('Password must contain at least one uppercase letter, one lowercase letter, and one number.');

    $errors = Validator::validate([
        'password' => '12345',
    ], [
        'password' => ['strong_password'],
    ]);

    expect($errors)->toHaveCount(1);
    expect($errors)->toHaveKey('password');
    expect($errors['password']['strong_password'])->toBe('Password must contain at least one uppercase letter, one lowercase letter, and one number.');

    $errors = Validator::validate([
        'password' => 'testTest1',
    ], [
        'password' => ['strong_password'],
    ]);

    expect($errors)->toHaveCount(0);
    expect($errors)->not->toHaveKey('password');
});

it('throws errors as exceptions', function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    Validator::throwErrors($errors);
})->throws(ValidationException::class);

it("doesn't throw exceptions when there are no errors", function () {
    $errors = Validator::validate([
        'name' => 'Jim Gordon',
    ], [
        'name' => ['required'],
    ]);

    expect($errors)->toHaveCount(0);

    Validator::throwErrors($errors);
});

it('keys the validation messages with the rule', function () {
    $errors = Validator::validate([
        'name' => '',
    ], [
        'name' => ['required'],
    ]);

    expect($errors['name'])->toHaveKey('required');
    expect($errors['name']['required'])->toBe('Name is required.');
});

it('uses user provided validation messages', function () {
    $errors = Validator::validate([
        'name' => '',
        'password' => '123456',
    ], [
        'name' => ['required'],
        'password' => ['max:4'],
    ], [
        'name' => [
            'required' => 'Custom message for required rule.',
        ],
        'password' => [
            'max:4' => 'Custom message for max:4 rule.',
        ],
    ]);

    expect($errors['name']['required'])->toBe('Custom message for required rule.');
    expect($errors['password']['max:4'])->toBe('Custom message for max:4 rule.');
});
