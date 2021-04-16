@extends('layouts.app')

@section('content')
<div class="container">
    <div class="float-start row justify-content-center">
        <button 
          type="button" 
          class="btn btn-success"
          onclick="createTrip()"
          data-toggle="modal"
          data-target="#createTrip">新增行程</button>
    </div><br>
    <div class="row justify-content-center">
        @if ( count( $trips ) )
        @foreach ( $trips as $trip )
        <?php $trip_id = $trip[ 'trip' ]->id; ?>

        <button 
            type="button" 
            class="btn btn-success btn-lg disabled" 
            style="margin-right: 5%;"
            onclick="document.location='/trip/viewPlayer/{{ $trip_id }}'">管理參加</button>

        <button 
            type="button" 
            class="btn btn-primary btn-lg disabled" 
            style="margin-right: 5%;"
            id="{{ $trip_id }}" 
            onclick="createAction( this.id )"
            data-toggle="modal" 
            data-target="#createLocation">新增地點</button>

        <figure>
          <blockquote class="blockquote">
            <h2>{{ $trip[ 'trip' ]->name }}</h2>
          </blockquote>
          <figcaption class="blockquote-footer">
            {{ $trip[ 'trip' ]->description }}
          </figcaption>
        </figure>

        <button type="button" class="btn btn-warning btn-lg disabled" style="margin-left: 5%;"
            id="{{ $trip_id }}-{{ $trip[ 'trip' ]->name }}-{{ $trip[ 'trip' ]->description }}" 
            onclick="updateTrip( this.id )"
            data-toggle="modal"
            data-target="#createTrip">編輯行程</button>

        <button type="button" class="btn btn-secondary btn-lg disabled" style="margin-left: 5%;"
            id="{{ $trip_id }}" 
            onclick="deleteTrip( this.id )"
            data-toggle="modal" 
            data-target="#deleteTrip">刪除行程</button>

        <table class="table" style="margin-top: 20px;">
            <thead>
                <tr>
                <th scope="col">順序</th>
                <th scope="col">地點名稱</th>
                <th scope="col">描述</th>
                <th scope="col">抵達方式</th>
                <th scope="col">抵達時間</th>
                <th scope="col">上移/下移</th>
                <th scope="col">修改/刪除</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                @foreach ( $trip[ 'locations' ] as $location )
                <tr>
                    <th scope="row">{{ $index }}</th>
                    <td>{{ $location[ 'location' ]->name }}</td>
                    <td>{{ $location[ 'location' ]->description }}</td>
                    <td>{{ $location[ 'arrival_method' ] }}</td>
                    <td>{{ $location[ 'time' ] }}</td>
                    <td>
                      <form action="/trip/locationOrder" method="POST">
                          @csrf
                          @method( "PUT" )
                            <input type="hidden" id="trip_id" name="trip_id" value="{{ $trip_id }}">
                            <input type="hidden" id="location_id" name="location_order" value="{{ $location[ 'order' ] }}">
                            <button type="submit" class="btn btn-info" name="change" value="upper">▲</button>
                            <button type="submit" class="btn btn-info" name="change" value="lower">▼</button>
                      </form>
                    </td>
                    <td>
                        <button 
                            type="button" 
                            class="btn btn-primary" 
                            id="{{ $trip_id }}-{{ $location[ 'order' ] }}" 
                            onclick="editAction( this.id )"
                            data-toggle="modal" 
                            data-target="#editLocation">編輯</button>
                        <button 
                            type="button" 
                            class="btn btn-danger" 
                            id="{{ $trip_id }}-{{ $location[ 'order' ] }}" 
                            onclick="deleteAction( this.id )"
                            data-toggle="modal" 
                            data-target="#deleteLocation">刪除</button>
                    </td>
                </tr>
                <?php $index += 1; ?>
                @endforeach
            </tbody>
        </table>
        @endforeach
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">修改地點</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/location" method="POST">
      @csrf
      @method( 'PUT' )
        <input type="hidden" id="edit_trip_id" name="trip_id" value="-1">
        <input type="hidden" id="edit_order_id" name="order_id" value="-1">
        <div class="modal-body">
            選擇想修改成的地點
        </div>

        <select class="form-select form-select-lg mb-3" style="margin-left: 5%; width: 90%;" id="location_id" name="location_id">
                @foreach ( $locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
        </select>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">刪除地點</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/location" method="POST">
      @csrf
      @method( 'DELETE' )
        <input type="hidden" id="delete_trip_id" name="trip_id" value="-1">
        <input type="hidden" id="delete_order_id" name="order_id" value="-1">

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">修改地點</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/location" method="POST">
      @csrf
        <input type="hidden" id="create_trip_id" name="trip_id" value="-1">
        <div class="modal-body">
            選擇想增加的地點
        </div>

        <select class="form-select form-select-lg mb-3" style="margin-left: 5%; width: 90%;" id="location_id" name="location_id">
                @foreach ( $locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
        </select>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteTrip" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">刪除行程</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/" method="POST">
      @csrf
      @method( 'DELETE' )
        <input type="hidden" id="delete_trip" name="trip_id" value="-1">

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createTrip" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manageTripTitle">noMessage</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/trip/" method="POST">
      <input type="hidden" id="edit_trip_method" name="_method" value="POST">
      @csrf
        <div class="row justify-content-center">
          <div class="row justify-content-center" style="width:100%; margin-top:10px;">
            <p class="h5">行程名稱</p>
          </div>
          <div style="width: 80%;">
            <input type="text" class="form-control" id="trip_name" name="trip_name" placeholder="Trip Name">
          </div>

          <div class="row justify-content-center" style="width:100%; margin-top:10px;">
            <p class="h5">行程描述</p>
          </div>
          <div style="width: 80%;">
            <input type="text" class="form-control" id="trip_desc" name="trip_desc" placeholder="Trip Description">
          </div>
        </div>

        <div class="modal-footer" style="margin-top: 10px;">
            <input type="hidden" id='update_trip_id' name='trip_id' value="-1">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">確定</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<script>
function editAction( id )
{
    var arrayOfStrings = id.split('-');
    trip_id = arrayOfStrings[0];
    order_id = arrayOfStrings[1];

    document.getElementById('edit_trip_id').value = trip_id;
    document.getElementById('edit_order_id').value = order_id;
}

function deleteAction( id )
{
    var arrayOfStrings = id.split('-');
    trip_id = arrayOfStrings[0];
    order_id = arrayOfStrings[1];

    document.getElementById('delete_trip_id').value = trip_id;
    document.getElementById('delete_order_id').value = order_id;
}

function createAction( id )
{
    trip_id = id;

    document.getElementById('create_trip_id').value = trip_id;
}

function createTrip()
{
    document.getElementById('manageTripTitle').innerHTML = "新增行程";
    document.getElementById('trip_name').value = "";
    document.getElementById('trip_desc').value = "";
    document.getElementById('update_trip_id').value = "";
    document.getElementById('edit_trip_method').value = "POST";
}

function updateTrip( content )
{
    var contentArray = content.split('-');
    var id = contentArray[0];
    var name = contentArray[1];
    var desc = contentArray[2];

    document.getElementById('manageTripTitle').innerHTML = "編輯行程";
    document.getElementById('trip_name').value = name;
    document.getElementById('trip_desc').value = desc;
    document.getElementById('update_trip_id').value = id;
    document.getElementById('edit_trip_method').value = "PUT";
    console.log( id );
}

function deleteTrip( id )
{
    trip_id = id;
    
    document.getElementById('delete_trip').value = trip_id;
}
</script>