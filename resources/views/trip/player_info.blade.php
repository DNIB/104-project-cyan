@extends('layouts.app')

@section('content')
<div class="container">
    <div class="float-start row justify-content-center">
        <figure>
            <blockquote class="blockquote">
                <h2>行程：{{ $trip->name }} 參加者名單</h2>
            </blockquote>
            <figcaption class="blockquote-footer">
                行程敘述：{{ $trip->description }}
            </figcaption>
        </figure>
    </div>

    <div id="display_info">
        <div class="row justify-content-center" style="margin-bottom: 10px;">
            <button type="button" class="btn btn-secondary" onclick="back()" style="margin-right: 10px;">返回</button>
            <button type="button" class="btn btn-success" onclick="createPlayer()">新增參加者</button>
        </div>

        <div class="row justify-content-center">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">名字</th>
                    <th scope="col">敘述</th>
                    <th scope="col">email</th>
                    <th scope="col">電話</th>
                    <th scope="col">修改</th>
                    <th scope="col">刪除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; ?>
                    @foreach ( $players as $player )
                        <?php $index += 1; ?>
                        <tr>
                            <th scope="row">{{ $index }}</th>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->description ?? "(無敘述資料)" }}</td>
                            <td>{{ $player->email ?? "(無 email 資料)" }}</td>
                            <td>{{ $player->phone ?? "(無電話資料)" }}</td>
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-primary" 
                                    id="{{ $player->id }}-{{ $player->name }}-{{ $player->description ?? null }}-{{ $player->email ?? null }}-{{ $player->phone ?? null }}" 
                                    onclick="updateAction( this.id )"
                                    data-toggle="modal" 
                                    data-target="#editPlayer">修改</button>
                            </td>
                            @if ( !$player->trip_creator )
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-danger" 
                                    id="{{ $player->id }}" 
                                    onclick="deleteAction( this.id )"
                                    data-toggle="modal" 
                                    data-target="#editPlayer">刪除</button>
                            </td>
                            @endif
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-center" style="display:none" id="display_creator">
        <form action="/trip/viewPlayer" method="POST">
        @csrf
            <input type="hidden" id="trip_id" name="trip_id" value="{{ $trip->id }}">

            <input class="form-control" type="text" id="name" name="name" placeholder="(name)">
            <input class="form-control" type="text" id="desc" name="desc" placeholder="(description)">
            <input type="email" class="form-control" id="email" name="email" placeholder="(name@example.com)">
            <input class="form-control" type="tel" id="phone" name="phone" placeholder="(phone)">


            <button type="submit" class="btn btn-info">確定</button>
            <button type="button" class="btn btn-secondary" onclick="cancelCreate()">取消</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editPlayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTitle">noMessage</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/viewPlayer" method="POST">
        @csrf
        <input type="hidden" id="request_method" name="_method" value="PUT">
        <input type="hidden" name="trip_id" value="{{ $trip->id }}">

        <div class="float-start row justify-content-center">
            <input class="form-control" id="edit_name" name="name" type="text" style="width: 80%; margin-top: 5px;" placeholder="(name)">
            <input class="form-control" id="edit_desc" name="desc" type="text" style="width: 80%; margin-top: 5px;" placeholder="(description)">
            <input class="form-control" id="edit_email" name="email" type="text" style="width: 80%; margin-top: 5px;" placeholder="(email)">
            <input class="form-control" id="edit_phone" name="phone" type="text" style="width: 80%; margin-top: 5px;" placeholder="(phone)">
        </div>

        <div class="modal-footer" style="margin-top: 10px;">
            <input type="hidden" id='player_id' name='player_id' value="-1">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

<script>
function createPlayer()
{
    document.getElementById('display_info').style.display = "none";
    document.getElementById('display_creator').style.display = "initial";
}

function cancelCreate()
{
    document.getElementById('display_info').style.display = "initial";
    document.getElementById('display_creator').style.display = "none";
}

function updateAction( player_info )
{
    content = player_info.split('-');

    id = content[0];
    name = content[1];
    desc = content[2];
    email = content[3];
    phone = content[4];

    changeTitle( "編輯參加者資訊" );
    openDisplay();
    
    document.getElementById( 'player_id' ).value = id;
    document.getElementById( 'edit_name' ).value = name;
    document.getElementById( 'edit_desc' ).value = (desc) ? desc : "";
    document.getElementById( 'edit_phone' ).value = (phone) ? phone : "";

    document.getElementById( 'request_method' ).value = "PUT";

    if ( email != "" ) {
        document.getElementById( 'edit_email' ).value = email;
        document.getElementById( 'edit_email' ).readOnly = true;
    } else {
        document.getElementById( 'edit_email' ).value = "";
        document.getElementById( 'edit_email' ).readOnly = false;
    }
}

function deleteAction( player_info )
{
    content = player_info.split('-');

    id = content[0];

    changeTitle( "刪除參加者資訊" );
    
    document.getElementById( 'player_id' ).value = id;
    document.getElementById( 'edit_name' ).type = "hidden";
    document.getElementById( 'edit_desc' ).type = "hidden";
    document.getElementById( 'edit_phone' ).type = "hidden";
    document.getElementById( 'edit_email' ).type = "hidden";

    document.getElementById( 'request_method' ).value = "DELETE";
}

function openDisplay()
{
    document.getElementById( 'edit_name' ).type = "text";
    document.getElementById( 'edit_desc' ).type = "text";
    document.getElementById( 'edit_phone' ).type = "text";
    document.getElementById( 'edit_email' ).type = "text";
}

function changeTitle( title )
{
    document.getElementById( 'editTitle' ).innerHTML = title;
}

function back()
{
    window.location = '/trip/index';
}
</script>