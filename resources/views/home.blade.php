@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
            <div class="card">
                <div class="card-header"> 管理資料 </div>
                <div class="card-body">
                @if ( Auth::user()->super_user )
                    @component ( 'manage.unit.manage_user' )
                    @endcomponent

                    @component ( 'manage.unit.toward_button')
                    @endcomponent
                @else
                    一般使用者
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function editAction( user_id )
{
    document.getElementById("user_table").style = "display: none;";
    document.getElementById("edit_table").style = "display: initail;";

    email = document.getElementById(user_id + "-email").innerHTML;
    name = document.getElementById(user_id + "-name").innerHTML;

    document.getElementById("id").value = user_id;
    document.getElementById("email").value = email;
    document.getElementById("name").value = name;
}

function cancelEdit()
{
    document.getElementById("user_table").style = "display: initail;";
    document.getElementById("edit_table").style = "display: none;";
}

function deleteAction( user_id )
{
    document.getElementById("user_table").style = "display: none;";
    document.getElementById("delete_table").style = "display: initail;";

    document.getElementById("delete_id").value = user_id;
}

function cancelDelete()
{
    document.getElementById("user_table").style = "display: initail;";
    document.getElementById("delete_table").style = "display: none;";

    document.getElementById("delete_id").value = "-1";
}
</script>