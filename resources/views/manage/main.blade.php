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
                                <?php $columns_string = ""; ?>
                                @foreach ( $columns as $column )
                                    <?php $columns_string = $columns_string.$column."-"?>
                                    <th scope="col">{{ $column }}</th>
                                @endforeach
                                <th scope="col">修改</th>
                                <th scope="col">刪除</th>
                            </tr>
                        </thead>
                        <meta id="columns" name="columns" content="{{ $columns_string }}">
                        <tbody>
                            @foreach ( $rows as $row )
                                <tr>
                                @foreach ($columns as $column )
                                    <th scope="row" id="{{ $row->id }}-{{ $column }}">{{ $row->$column }}</th>
                                @endforeach
                                <th scope="row">
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        onclick="updateAction( this.id )"
                                        id="{{ $row->id }}"
                                        data-toggle="modal"
                                        data-target="#editor">修改
                                    </button>
                                </th>
                                <th scope="row">
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        id="{{ $row->id }}"
                                        onclick="deleteAction( this.id )"
                                        data-toggle="modal"
                                        data-target="#editor">刪除
                                    </button>
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

<!-- Modal -->
<div class="modal fade" id="editor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="input_title">noMessage</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/user/{{ $type }}" method="POST">
        @csrf
        <input type="hidden" id="request_method" name="_method" value="PUT">
        <input type="hidden" id="id" name="id">

        <div id="input_area">
            <div class="row justify-content-center">
            @foreach ( $columns as $column )
                @if ( $column != 'id' )
                    <input 
                        type="text" 
                        class="form-control" 
                        id="{{ $column }}" 
                        name="{{ $column }}" 
                        placeholder="{{ $column }}"
                        style="margin-top: 5px; margin-bottom: 5px; width: 80%;">
                @endif
            @endforeach
            </div>
        </div>

        <div class="modal-footer" style="margin-top: 10px;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<script>
function updateAction( id )
{
    document.getElementById('input_area').style.display = "initial";
    document.getElementById('input_title').innerHTML = "編輯內容";
    document.getElementById('request_method').value = 'PUT';

    columns = document.getElementById('columns').content;
    columns_array = columns.split('-');

    columns_array.forEach( column => {
        if ( column != "") {
            target_id = id+"-"+column;
            target_document = document.getElementById( target_id );
            target_text = target_document.innerHTML;

            document.getElementById( column ).value = target_text;
        }
    } );
}

function deleteAction( id )
{
    document.getElementById('input_area').style.display = "none";
    document.getElementById('input_title').innerHTML = "確認刪除？";
    document.getElementById('request_method').value = 'DELETE';

    target_id = id+"-id";
    id_valie = document.getElementById( target_id ).innerHTML;
    document.getElementById('id').value = id_valie;
}
</script>