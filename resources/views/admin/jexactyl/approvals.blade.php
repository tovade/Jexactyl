@extends('layouts.admin')
@include('partials/admin.jexactyl.nav', ['activeTab' => 'approvals'])

@section('title')
    User Approvals
@endsection

@section('content-header')
    <h1>User Approvals<small>Allow or deny requests to create accounts.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Jexactyl</li>
    </ol>
@endsection

@section('content')
    @yield('jexactyl::nav')
    <form action="{{ route('admin.jexactyl.approvals') }}" method="POST">
        <div class="row">
            <div class="col-xs-12">
                <div class="box
                    @if($enabled == 'true')
                        box-success
                    @else
                        box-danger
                    @endif
                ">
                    <div class="box-header with-border">
                        <i class="fa fa-users"></i>
                        <h3 class="box-title">Approval System <small>Decide whether the approval system is enabled or disabled.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Enabled</label>
                                <div>
                                    <select name="enabled" class="form-control">
                                        <option @if ($enabled == 'false') selected @endif value="false">Disabled</option>
                                        <option @if ($enabled == 'true') selected @endif value="true">Enabled</option>
                                    </select>
                                    <p class="text-muted"><small>Determines whether users must be approved by an admin to use the Panel.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" for="webhook">Webhook URL</label>
                                <input name="webhook" id="webhook" class="form-control" value="{{ $webhook }}">
                                <p class="text-muted"><small>Provide the Discord Webhook URL to use when a user needs to be approved.</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="box box-footer">
                        {!! csrf_field() !!}
                        <button type="submit" name="_method" value="PATCH" class="btn btn-default pull-right">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <i class="fa fa-list"></i>
                    <h3 class="box-title">Approval Requests <small>Allow or deny requests to create accounts.</small></h3>
                 </div>
                <div style="display: flex; align-items: end; justify-content: end">
                    <form class="text-right" id="approveallform" action="{{ route('admin.jexactyl.approvals.approve', 0) }}" method="POST">
                        {!! csrf_field() !!}
                        <button id="approvalApproveAllBtn" class="btn btn-xs btn-default">
                            Approve all
                        </button>
                    </form>
                    <form class="text-right" id="denyallform" action="{{ route('admin.jexactyl.approvals.deny', 0) }}" method="POST">
                        {!! csrf_field() !!}
                        <button id="approvalDenyAllBtn" class="btn btn-xs btn-default">
                            Deny all
                        </button>
                    </form>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Registered</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <code>{{ $user->id }}</code>
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        <code>{{ $user->username }}</code> ({{ $user->name_first }} {{ $user->name_last }})
                                    </td>
                                    <td>
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-center">
                                        <form id="approveform" action="{{ route('admin.jexactyl.approvals.approve', $user->id) }}" method="POST">
                                            {!! csrf_field() !!}
                                            <button id="approvalApproveBtn" class="btn btn-xs btn-default">
                                                <i class="fa fa-check text-success"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form id="denyform" action="{{ route('admin.jexactyl.approvals.deny', $user->id) }}" method="POST">
                                            {!! csrf_field() !!}
                                            <button id="approvalDenyBtn" class="btn btn-xs btn-default">
                                                <i class="fa fa-times text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script>
    $('#approvalDenyBtn').on('click', function (event) {
        event.preventDefault();

        swal({
            type: 'error',
            title: 'Deny this request?',
            text: 'This will remove this user from the Panel immediately.',
            showCancelButton: true,
            confirmButtonText: 'Deny',
            confirmButtonColor: 'red',
            closeOnConfirm: false
        }, function () {
            $('#denyform').submit()
        });
    });

    $('#approvalApproveBtn').on('click', function (event) {
        event.preventDefault();

        swal({
            type: 'success',
            title: 'Approve this request?',
            text: 'This user will gain access to the Panel immediately.',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            confirmButtonColor: 'green',
            closeOnConfirm: false
        }, function () {
            $('#approveform').submit()
        });
    });
    $('#approvalDenyAllBtn').on('click', function (event) {
        event.preventDefault();

        swal({
            type: 'error',
            title: 'Deny all requests?',
            text: 'This will remove all users that requested an approval from the panel.',
            showCancelButton: true,
            confirmButtonText: 'Deny',
            confirmButtonColor: 'red',
            closeOnConfirm: false
        }, function () {
            $('#denyallform').submit()
        });
    });
    $('#approvalApproveAllBtn').on('click', function (event) {
        event.preventDefault();

        swal({
            type: 'success',
            title: 'Approve these requests?',
            text: 'This will grant all users that requested an approval access to the panel immediately.',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            confirmButtonColor: 'green',
            closeOnConfirm: false
        }, function () {
            $('#approveallform').submit()
        });
    });
    </script>
@endsection
