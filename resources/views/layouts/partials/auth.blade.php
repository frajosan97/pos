<div class="container-fluid d-flex align-items-center justify-content-center auth-container">

    <div class="col-md-4 mx-auto">

        <div class="card border-0 rounded-4 shadow mb-3">
            <div class="card-body p-4">
                <!-- Logo -->
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('assets/images/logo/'.$company_info->logo ?? 'logo.png') }}" class="img-fluid"
                        alt="{{ $company_info->name }}" style="max-width: 100px; max-height: 100px;">
                </div>

                <!-- content -->
                @yield('content')

            </div>
        </div>

    </div>

</div>