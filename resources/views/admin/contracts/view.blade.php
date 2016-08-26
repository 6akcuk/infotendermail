@extends('layouts.admin-ibox')

@section('header', 'Контракты')

@section('breadcrumbs')
    <li>Контракты</li>
@endsection

@section('ibox-title', trans_choice('main.contracts', sizeof($list)))

@section('ibox-content')
    <div class="table-responsive">
        <table id="contracts-table" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>Название</th>
                <th>Организация</th>
                <th>Адрес</th>
                <th>ИНН</th>
                <th>Цена</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list as $contract)
            <tr>
                <td>
                    <a href="{{ !stristr($contract->link, 'zakupki.gov.ru') ? 'http://zakupki.gov.ru'. $contract->link : $contract->link }}">
                        {{ $contract->name }}
                    </a>
                </td>
                <td>{{ $contract->organization->name }}</td>
                <td>{{ $contract->organization->address }}</td>
                <td>{{ $contract->organization->inn }}</td>
                <td style="width: 180px">{{ number_format($contract->price, 2, ',', ' ') }} руб.</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('footer_js')
    <!-- Data Tables -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/dataTables/dataTables.responsive.js"></script>
    <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script>

    <script>
        $(document).ready(function() {
            //$('#contracts-table').dataTable({
            //});
        });
    </script>
@endsection