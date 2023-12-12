<?php
use \App\Helpers\CurrencyConverter;
/**
 * @var \App\Models\User $user
 * @var \App\Models\Partner $partner
 * @var array $errors
 */
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<form action="" class="container mt-5" method="post">
    @csrf
    <div class="row justify-content-center">
        <div class="col-auto">
            @if (!empty($errors))
                <div class="alert alert-danger">
                    @foreach($errors as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <h4>User: {{ $user->name }}, partner: {{ $partner->name }}</h4>
            <table class="table table-condensed">
                <tbody>
                <tr>
                    <td>Баланс пользователя</td>
                    <td>{{ CurrencyConverter::satoshiToBitcoin($user->balance) }} BTC</td>
                    <td>

                        <label for="user_balance_value">Сумма, Сатоши</label>
                        <input type="number" class="form-control mb-2" name="user_balance_value" id="user_balance_value">
                        <button class="btn btn-success" type="submit" name="user_balance_send" value="1">Перевести</button>
                    </td>
                </tr>
                <tr>
                    <td>Баланс сайта</td>
                    <td colspan="2">{{ CurrencyConverter::satoshiToBitcoin(\App\Models\SiteBalance::getBalance()) }} BTC</td>
                </tr>
                <tr>
                    <td>Баланс партнера сайта</td>
                    <td>{{ CurrencyConverter::satoshiToBitcoin($partner->balance) }} BTC</td>
                    <td>
                        <button class="btn btn-info" type="submit" name="partner_balance_send" value="1">Забрать</button>
                    </td>
                </tr>
                <tr>
                    <td>Кэшбэк пользователя</td>
                    <td>{{ CurrencyConverter::satoshiToBitcoin($user->cashback) }} BTC</td>
                    <td>
                        <button class="btn btn-warning" type="submit" name="user_cashback_send" value="1">Вернуть</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>
</body>
</html>
