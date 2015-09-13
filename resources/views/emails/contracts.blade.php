@extends('layouts.email')

@section('title', 'Контракты')

@section('content')
    <table class="main" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-wrap aligncenter">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="content-block">
                            <h2>{{ trans_choice('main.contracts', sizeof($list)) }}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            <table class="invoice">
                                <tr>
                                    <td>
                                        <table class="invoice-items" cellpadding="0" cellspacing="0">
                                            @foreach ($list as $contract)
                                            <tr>
                                                <td>
                                                    <a href="{{ $contract->link }}">
                                                        {{ $contract->name }}
                                                    </a>
                                                </td>
                                                <td width="150" class="alignright">
                                                    {{ number_format($contract->price, 2, ',', ' ') }} руб.
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            <a href="{{ route('admin.contracts.view', ['date' => date('Y-m-d')]) }}">Просмотреть в браузере</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            infotendermail.ru &copy; 2015
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection