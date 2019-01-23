@extends('layouts.bst')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <div>
        <h2>图片上传</h2>
        <form action="/updateimg" method="post" enctype="multipart/form-data">
            <p>
                <input type="file" name="img">
            </p>
            <p>
                <input type="submit" value="UPDATE">
            </p>
        </form>
    </div>
@endsection

@section('footer')
    @parent
@endsection