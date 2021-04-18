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
                    @foreach ( $players as $player)
                        <?php $player_info = $player->player()->get()[0]; ?>
                        <?php $index += 1; ?>
                        <tr>
                            <th scope="row">{{ $index }}</th>
                            <td>{{ $player_info->name }}</td>
                            <td>{{ $player_info->description ?? "(無敘述資料)" }}</td>
                            <td>{{ $player_info->email ?? "(無 email 資料)" }}</td>
                            <td>{{ $player_info->phone ?? "(無電話資料)" }}</td>
                            <td>
                                <button type="button" class="btn btn-primary">修改</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger">刪除</button>
                            </td>
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

            <input class="form-control" type="text" id="name" name="name" placeholder="(name))">
            <input class="form-control" type="text" id="desc" name="desc" placeholder="(description)">
            <input type="email" class="form-control" id="email" name="email" placeholder="(name@example.com)">
            <input class="form-control" type="tel" id="phone" name="phone" placeholder="(phone)">


            <button type="submit" class="btn btn-info">確定</button>
            <button type="button" class="btn btn-secondary" onclick="cancelCreate()">取消</button>
        </form>
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
</script>