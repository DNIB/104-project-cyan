<form action="/location" method="POST">
    @csrf
    @method ( 'DELETE' )
    <div class="display-table">
        <div class="select-text" onclick="selectAction(this.id)" id="{{ $id }}" name="{{ $id }}"> 
            {{ $name }} 
        </div>
        <input type="hidden" id="location_id" name="location_id" value="{{ $id }}">
        <input class="select-button btn btn-success" type="button" value="修改" id="{{ $id }}" onclick="updateAction(this.id)">
        <input class="select-button btn btn-danger" type="submit" value="刪除">
    </div>
</form>