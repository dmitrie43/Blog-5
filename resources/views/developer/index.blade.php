@extends('layout')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="main-content">
        <div class="container">
            <section class="content">
            {{Form::open([
            'route' => 'developer.store',
            'files' => true
            ])}}
            <!-- Default box -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Ваши посты</h3>
                        @include('admin.errors')
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <a href="{{route('developer.create')}}" class="btn btn-success">Добавить</a>
                        </div>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Категория</th>
                                <th>Теги</th>
                                <th>Картинка</th>
                                <th>Изменить</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>{{$post->title}}
                                    </td>
                                    <td>{{$post->getCategoryTitle()}}</td>
                                    <td>{{$post->getTagsTitles()}}</td>
                                    <td>
                                        <img src="{{$post->getImage()}}" alt="" width="100">
                                    </td>
                                    <td style="text-align: center">
                                        <a href="{{route('developer.edit', $post->id)}}" class="fa fa-pencil"></a>
                                    </td>
                                    <td style="text-align: center">
                                        {{Form::open(['route' => ['developer.destroy', $post->id], 'method' => 'delete'])}}
                                        {{--<form action="{{route('posts.destroy', [$post->id])}}" method="post">--}}
                                        <button onclick="return confirm('Вы уверены?')" type="submit" class="delete">
                                            <a class="fa fa-remove"></a>
                                        </button>
                                        {{--</form>--}}
                                        {{Form::close()}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                {{Form::close()}}
            </section>
        </div>
    </div>
    <!-- /.content-wrapper -->
@endsection