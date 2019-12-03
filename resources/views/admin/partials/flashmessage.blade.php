@if(Session::has('message'))
    <div class="row">
        <div class='col-md-12'>
            <p class="alert alert-{{ Session::get('message-type', 'info') }}  alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ Session::get('message') }}
            </p>
        </div>
    </div>
@endif