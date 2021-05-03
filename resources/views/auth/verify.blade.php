@extends('base')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Activate account</p>
                <form action="{{route('verify')}}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input name="code" type="text" class="form-control" placeholder="Code">
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <!-- /.social-auth-links -->

            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
