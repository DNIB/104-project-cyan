<form action="/location/{{ $action }}" method="{{ $method }}">
    @csrf
    @if ( $action == 'update' )
        <input type="hidden" id="location_id" name="location_id" value="-1">
    @else
    <div class="inner">
        <label for="lat_value"> 緯度 </label>
        <h3 name="lat_value" id="lat_value"> (No Value) </h3>
        <label for="lng_value"> 經度 </label>
        <h3 name="lng_value" id="lng_value"> (No Value) </h3>
    </div>
    <input type="hidden" id="lat_submit" name="lat_submit" value="no_value">
    <input type="hidden" id="lng_submit" name="lng_submit" value="no_value">
    @endif

    <label for="select_name" class="inner"> 地點名稱 </label>
    <input type="text" class="inner" id="select_name" name="select_name" value="（地點名稱）">

    <label for="select_des" class="inner"> 地點描述 </label>
    <textarea class="inner text-submit" id="select_desc" name="select_desc">（地點描述）</textarea>

    <input type="submit" class="inner button-submit" value="Submit">
    <input type="button" class="inner button-submit" value="Cancel" onclick="cancelUpdateAction()">

    <input type="hidden" id="user_id" name="user_id" value="{{ Auth::id() }}">
</form>