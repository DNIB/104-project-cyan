<div class="card-header"> 管理帳號 </div>
<div class="card-body" id="user_table" name="user_table">
    <?php
        $users = App\User::all();
    ?>
    <table class="table">
        <thead>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
        </thead>
        <tbody>
            @foreach ( $users as $user )
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td id="{{ $user->id }}-name">{{ $user->name }}</td>
                    <td id="{{ $user->id }}-email">{{ $user->email }}</td>
                    <td>
                        <button 
                            type="button" 
                            class="btn btn-primary" 
                            id="{{ $user->id }}" 
                            onclick="editAction( this.id )"> Edit </button>
                    </td>
                    <td>
                        <button 
                            type="button" 
                            class="btn btn-danger" 
                            id="{{ $user->id }}" 
                            onclick="deleteAction( this.id )"> Delete </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="card-body" style="display: none;" id="edit_table" name="edit_table">
    <form action="/user" method="POST">
    @method( 'PUT' )
    @csrf
        <input type="hidden" id="id" name="id" value="-1"> 

        <label for="name" class="form-label">Email address</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name">

        <br>
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">

        <br>
        <label for="password" class="form-label">Password（保持為空則維持原密碼）</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="********">

        <br>
        <button type="submit" class="btn btn-primary"> 更改會員資料 </button>
    </form>

    
</div>

<div class="card-body" style="display: none;" id="delete_table" name="delete_table">
    <form action="/user" method="POST">
    @method( 'DELETE' )
    @csrf
        <input type="hidden" id="delete_id" name="delete_id" value="-1"> 
        <button type="submit" class="btn btn-primary"> 確認刪除 </button>
        <button type="button" class="btn btn-danger" onclick="cancelDelete()"> 取消刪除 </button>
    </form>
</div>