@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Incentive Attachments</h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
           
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-3">
                    <table class="table table-bordered  table-row-bordered" id="system_datatable22222">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4">Ref ID</th>
                                <th>Incentive Period</th>
                                <th>Incentive Attachment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                ];
                            @endphp
                                @for ($month = 1; $month <= 12; $month++)
                                    <tr>
                                        <td class="ps-5">
                                            {{ $month }}
                                        </td>
                                        <td>
                                            {{ $months[$month] }} - {{ date('Y') }}
                                        </td>
                                        <td>
                                            <input type="file" class="form-control" name="attachments">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" style="width:100%;">Save</button>
                                        </td>
                                    </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js-link')

@endpush