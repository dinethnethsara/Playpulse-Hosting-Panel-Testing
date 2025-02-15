@extends('layouts.admin')

@section('title')
	Example Plugin
@endsection

@section('content-header')
	<h1>Example Plugin<small>Configure plugin settings</small></h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('admin.index') }}">Admin</a></li>
		<li class="active">Example Plugin</li>
	</ol>
@endsection

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Settings</h3>
				</div>
				<form action="{{ route('example-plugin.settings.update') }}" method="POST">
					@csrf
					<div class="box-body">
						@if (session('success'))
							<div class="alert alert-success">
								{{ session('success') }}
							</div>
						@endif

						<div class="form-group">
							<label for="example_setting">Example Setting</label>
							<input type="text" name="example_setting" id="example_setting" 
								   class="form-control" value="{{ $settings['example_setting'] ?? '' }}"
								   required>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection