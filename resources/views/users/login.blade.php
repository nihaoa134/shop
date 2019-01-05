@extends('layouts.bst')


@section('content')

    <table  class="table table-bordered" style="margin: auto">
        <thead>
        <form  action="/login" method="post">
            <button type="reset" class="btn btn-danger">登陆</button>
            {{csrf_field()}}
            <div class="form-group">
                <label for="exampleInputName2">用户名</label>
                <input type="text" class="form-control" id="exampleInputName2" style="width: 200px" name="u_name" placeholder="请输用户名">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">密码</label>
                <input type="password" class="form-control"  name="u_pwd" style="width: 200px" id="exampleInputPassword1" placeholder="请输入密码">
            </div>
            <button type="submit" class="btn btn-success">login</button>
        </form>

        </tbody>
    </table>
@endsection
