<div class="display-table">
    <div class="select-text" onclick="selectAction(this.id)" id="{{ $id }}" name="{{ $id }}"> 
        {{ $name }} 
    </div>

    <input class="select-button" type="button" value="修改" id="{{ $id }}" onclick="updateAction(this.id)">
    <input class="select-button" type="button" value="刪除" id="{{ $id }}" onclick="deleteAction(this.id)">
</div>