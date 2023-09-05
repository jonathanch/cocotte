
@if (Session::has('error'))
<div class="alert alert-info alert-dismissible">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<h5><i class="icon fas fa-info"></i> Erreur!</h5> {{ Session::get('error') }}
</div>
@endif

@if (Session::has('success'))
<div class="alert alert-success alert-dismissible">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<h5><i class="icon fas fa-check"></i> Ok!</h5> {{ Session::get('success') }}
</div>
@endif
