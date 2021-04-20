@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                @component ( 'manage.unit.toward_button' )
                @endcomponent
            </div>
            <div class="card">
                <div class="card-header">{{ $name }}</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                @foreach ( $columns as $column )
                                    <th scope="col">{{ $column }}</th>
                                @endforeach
                                <th scope="col">修改</th>
                                <th scope="col">刪除</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $rows as $row )
                                <tr>
                                @foreach ($columns as $column )
                                    <th scope="row">{{ $row->$column }}</th>
                                @endforeach
                                <th scope="row">
                                    <button type="button" class="btn btn-primary">修改</button>
                                </th>
                                <th scope="row">
                                    <button type="button" class="btn btn-danger">刪除</button>
                                </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection