@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                   My Company Letters
                </h1>
            </div>

        </div>

    </div>

    <div class="app-content flex-column-fluid">

        <div class="app-container">

            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">

                @if(!empty($letters))
                    @foreach ($letters as $letter)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 ">
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <a href="{{ url('/download-personal-letter?path=letter/'.$letter['document_name']) }}&title=employee personal letter" target="_blank" data-bs-toggle="tooltip" title="{{ $letter['document_name'] }}">
                                        @php
                                            $document_name = explode(".",$letter['document_name']);
                                        @endphp
                                        <div class="symbol symbol-60px mb-5">
                                            @if(strtolower(end($document_name))=="txt")
                                                <img  src="{{ asset('portal_assets/document_icons/text.png') }}" />
                                            @elseif(strtolower(end($document_name))=="pdf")
                                               <img  src="{{ asset('portal_assets/document_icons/pdf.png') }}" />
                                            @elseif(strtolower(end($document_name))=="docx")
                                                <img  src="{{ asset('portal_assets/document_icons/docx.png') }}" />
                                            @elseif(strtolower(end($document_name))=="jpg")
                                                <img  src="{{ asset('portal_assets/document_icons/jpg.png') }}" />
                                            @elseif(strtolower(end($document_name))=="jpeg")
                                                <img  src="{{ asset('portal_assets/document_icons/jpeg.png') }}" />
                                            @elseif(strtolower(end($document_name))=="png")
                                                <img  src="{{ asset('portal_assets/document_icons/png.png') }}" />
                                            @elseif(strtolower(end($document_name))=="csv")
                                                <img  src="{{ asset('portal_assets/document_icons/csv.png') }}" />
                                            @elseif(strtolower(end($document_name))=="gif")
                                                <img  src="{{ asset('portal_assets/document_icons/gif.png') }}" />
                                            @elseif(strtolower(end($document_name))=="sql")
                                                <img  src="{{ asset('portal_assets/document_icons/sql.png') }}" />
                                            @elseif(strtolower(end($document_name))=="xlsx")
                                                <img  src="{{ asset('portal_assets/document_icons/xlsx.png') }}" />
                                            @endif
                                                                            
                                        </div>
                                        <div class="fs-5 fw-bold mb-2">
                                            {{ ($letter['letter_title']) ?? 'Document' }}
                                        </div>
                                    </a>
                                    <div class="fs-7 fw-semibold text-gray-400">
                                        {{ date('d M, Y', strtotime($letter['upload_date'])) }}                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach  
                @else
                    <div class="d-flex flex-column flex-center">
                        <img src="{{ asset('portal_assets/images/no-file.png') }}" class="mw-400px">
                        <div class="fs-1 fw-bolder text-dark mb-4">No Letters found.</div>
                        <div class="fs-6">Dear Employee, HR didn't upload your personal Letters yet. Please wait for a while.</div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection

