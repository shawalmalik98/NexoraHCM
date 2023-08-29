@extends('layout.main')

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">
    
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Document(s) of {{ $employee_data[0]['employee_name']?? '' }}
                    </h1>
                </div>

                <div class="d-flex align-items-center on-check d-none">
                        
                    <a href="javascript:void(0)" class="document_request text-white  btn btn-sm fw-bold btn-danger mx-2" data-value="Delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Document">Delete</a>
    
                </div>

            </div>
    
        </div>
                                                   
        <div class="app-content flex-column-fluid">
            
            <div class="app-container">
                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                    
                    @if(!empty($document_attachments))
                        @foreach ($document_attachments as $attachment)
                            <div class="col-md-6 col-lg-4 col-xl-3" >
                                <div class="card h-100 ">
                                    <div class="card-title m-6">
                                        <div style="display: flex; justify-content: flex-end;">
                                            <label class="form-check form-check-custom form-check-solid">
                                                <input class="document_request form-check-input h-20px w-20px checked" type="checkbox" data-id="{{$attachment['document_id']}}" value="{{$attachment['document_id']}}" />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8" @if(request()->get('document-id') != '' && $attachment['document_id'] == request()->get('document-id')) style="background-color:bisque;" @endif>

                                        @php $document_name = explode(".",$attachment['document_name']) @endphp

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
                                            {{ ($attachment['document_title']) ?? 'Document' }} 
                                        </div>

                                        <div class="fs-7 fw-semibold text-gray-400">
                                            {{ date('d M, Y', strtotime($attachment['document_upload_date'])) }}                     
                                        </div>

                                        <div class="text-center mt-5">
                                            <a href="{{ url('/employees/download-employee-document?path=employee_docs/'.$attachment['employee_id'].'/'.$attachment['document_name']) }}&title=Download Employee Document&action=view" data-bs-toggle="tooltip" title="View Document" class="btn btn-success btn-sm" target="_blank">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="{{ url('/employees/download-employee-document?path=employee_docs/'.$attachment['employee_id'].'/'.$attachment['document_name']) }}&title=Download Employee Document&action=download" data-bs-toggle="tooltip" title="Download Document" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                                <button class="btn btn-danger btn-sm document_request" type="button" data-id="{{$attachment['document_id']}}" data-value="Delete" data-bs-toggle="tooltip" title="Delete Document">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>  
                                                <button class="mt-3 btn btn-info btn-sm edit_btn" type="button"  data-bs-toggle="tooltip" title="Edit Documents" onclick="editDocuments('{{$attachment['document_id']}}','{{$attachment['document_title']}}','{{$attachment['document_name']}}')">
                                                        <i class="fa-solid fa-pencil"></i>
                                                </button>
                                             

                                        </div>
                                    </div>
                                    
                                </div>
                                   
                            </div>

                          
                        @endforeach
                    @endif

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="javascript:void(0);" class="text-hover-primary fs-5 fw-bold mb-2" data-bs-toggle="modal" data-bs-target="#addDocument">
                            <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8">
                                <svg xmlns="http://www.w3.org/2000/svg" width="67" height="67" viewBox="0 0 67 67" fill="none">
                                    <path opacity="0.25" d="M8.375 11.167C8.375 6.54161 12.1246 2.79199 16.75 2.79199H43.9893C46.2105 2.79199 48.3407 3.67436 49.9113 5.24497L56.172 11.5057C57.7426 13.0763 58.625 15.2065 58.625 17.4277V55.8337C58.625 60.459 54.8754 64.2087 50.25 64.2087H16.75C12.1246 64.2087 8.375 60.459 8.375 55.8337V11.167Z" fill="#00A3FF"/>
                                    <path d="M41.875 5.28162C41.875 3.90663 42.9896 2.79199 44.3646 2.79199V2.79199C46.3455 2.79199 48.2452 3.57889 49.6459 4.97957L56.4374 11.7711C57.8381 13.1718 58.625 15.0715 58.625 17.0524V17.0524C58.625 18.4274 57.5104 19.542 56.1354 19.542H44.6667C43.1249 19.542 41.875 18.2921 41.875 16.7503V5.28162Z" fill="#00A3FF"/>
                                    <path d="M32.4311 25.3368C32.1018 25.4731 31.7933 25.675 31.5257 25.9427L23.1507 34.3177C22.0605 35.4079 22.0605 37.1755 23.1507 38.2657C24.2409 39.3559 26.0085 39.3559 27.0987 38.2657L30.708 34.6563V47.4583C30.708 49.0001 31.9579 50.25 33.4997 50.25C35.0415 50.25 36.2913 49.0001 36.2913 47.4583V34.6563L39.9007 38.2657C40.9909 39.3559 42.7585 39.3559 43.8487 38.2657C44.9389 37.1755 44.9389 35.4079 43.8487 34.3177L35.4737 25.9427C34.6511 25.1201 33.443 24.9182 32.4311 25.3368Z" fill="#00A3FF"/>
                                </svg>

                                <div class="text-hover-primary fs-5 fw-bold mb-2 mt-5" data-bs-toggle="modal" data-bs-target="#addDocument">Add New Document</div>

                                <div class="fs-7 fw-semibold text-gray-400 text-center">
                                    Click here to add new document
                                </div>
                            </div>
                        </a>   
                    </div>

                </div>
            
            </div>
        
        </div>
    </div>

    
    <form id="addDocumentForm" class="form">
        @csrf
        <div class="modal fade" id="addDocument" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Add Document(s) of {{ $employee_data[0]['employee_name'] ?? 'Employee' }}</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="modal-body scroll-y pt-0 pb-15">
                    
                        <input type="hidden" name="file_length" value="1">
                        <div class="row mt-5" >
                            
                            <div class="col-md-5">
                                <label class="fs-6 fw-semibold mb-2 required">Title </label>
                            </div>

                            <div class="col-md-5">
                                <label class="required fs-6 fw-semibold mb-2">Document </label>
                            </div>

                            <div class="col-md-1">
                                <label class="fs-6 fw-semibold mb-6"></label>
                            </div>

                            <div class="col-md-1">
                                <label class="fs-6 fw-semibold mb-6"></label>
                            </div>
                        </div>
                        <div id="file_div">
                            <div class="row" >
                                
                                <input type="hidden" value="{{ Request::segment(3) }}" name="employee_id" id="employee_id">
                                <div class="col-md-5">
                                    <input type="text" class="form-control " name="title[]" placeholder="Enter Title e.g CNIC, Degree etc" required="">
                                </div>

                                <div class="col-md-5">
                                    <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*" name="file[]"  placeholder="Document" required="">
                                </div>

                                <div class="col-md-1">
                                    <button class="btn btn-icon btn-light-primary me-3" type="button" onclick="addfileRow()">
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                            </svg>
                                        </span>              
                                    </button>
                                </div>

                                <div class="col-md-1">
                                    <button class="btn btn-icon btn-light-danger" type="button" onClick="removeDiv(this)">
                                        <span class="svg-icon svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor"></rect>
                                            <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor"></rect>
                                            </svg>
                                        </span>        
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="edit_employee_documents_form" class="form">
        @csrf
        <div class="modal fade" id="edit_employee_documents_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Edit Document</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>

                    <div class="modal-body scroll-y">
                        
                        <input type="hidden" name="document_id" />
                            
                        <div class="row">
                          
                            <div class="col-md-6">
                                <label class="required fs-6 fw-semibold mb-2">Title</label>
                                <input type="text" class="form-control " name="title"  placeholder="Enter Title e.g CNIC, Degree etc" required="">
                            </div>
                           
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold mb-2">Documents</label>
                                <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*" name="file"  placeholder="Document" >
                            </div>
                        </div>

                       
                        
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_submit_btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

   
    {{--end::Modal - New Target--}}

     {{--- Delete employee Request --------------}}
    <div class="modal fade" tabindex="-1" id="delete-employee-document">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Confirmation Alert</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="delete_employee_document" class="form" >
                    @csrf
                    
                    <div class="modal-body">
                        <p>Are you sure to delete this Employee Document(s) ?</p>
                    </div>
        
                    <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="delete_submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Delete</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>


@endsection

@push('js-link')
<script src="{{url('assets/js/employees/employee-documents.js')}}"></script>
    
@endpush