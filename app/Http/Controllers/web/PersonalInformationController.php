<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\{Employees, EmployeeInformation, Bank};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\ActivityTracker;
use PDF;


class PersonalInformationController extends Controller
{
   
    use LoginEmployee, LogGenerateTrait;
	
   
    const SERVER_ERROR = 'Unknown error occured on server. Please try again after some time.';
    

    protected function index(){
        
        try{

            $title = '';

            $employee_data = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id', session('user_session'))->get()->toArray();
            
            $information_data = EmployeeInformation::where('employeeid', session('user_session'))->get()->toArray();
         
            $all_banks = Bank::get()->toArray();
           
            $relations = ['Father','Mother','Brother','Sister','Guardian'];  

            $disabled = 'FALSE';

            $send_data = compact('title','employee_data','information_data','all_banks','disabled','relations');

            return view('personal-information.form',$send_data);

        }catch(\Exception $e){
            $this->create_error_log('View Personal Information Page', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('View Personal Information Page');
           
        }


    }

    protected function view_employee_info_form($employee_id){


        try{
          
            $title = '';

            $employee_data = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',  $employee_id)->get()->toArray();
    
            $information_data = EmployeeInformation::where('employeeid',$employee_id)->get()->toArray();
         
            $all_banks = Bank::get()->toArray();
            
            $disabled = 'FALSE';

            $send_data = compact('title','employee_data','information_data','all_banks','disabled');
            
            return view('personal-information.form',$send_data);

        }catch(\Exception $e){
            $this->create_error_log('View Personal Information Page', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity("View Employee's Personal Information Page");
           
        }

		
	}

    protected function print_employee_info_form($employee_id){


        try{

            $title = '';
            $employee_data = Employees::where('employee_id',  $employee_id)->get()->toArray();
            
            $information_data = EmployeeInformation::where('employeeid',$employee_id)->get()->toArray();
         
            $all_banks = Bank::get()->toArray();
           
            $disabled = 'TRUE';

            $send_data = compact('title','employee_data','information_data','all_banks','disabled');

            
            $pdf = PDF::loadView('personal-information.pdf-download', $send_data);
     
            return $pdf->stream('Employee Information Form '.$send_data['employee_data'][0]['employee_name'].'-'.$send_data['employee_data'][0]['employee_code'].'.pdf');
            
        }catch(\Exception $e){
            $this->create_error_log('Print Employees Personal Information Document', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

        	ActivityTracker::track_activity("Print Employee's Personal Information Document");

        
        }
      
		
	}

    protected function submit_information(Request $informationData){

        try{
            $employee_record = Employees::where('employee_id',$informationData->input('employeeid'))->get(['employee_id','employee_code','employee_name','employee_personal_email','employee_department','employee_designation','employee_contactno'])->toArray();
            
            $submitInformation = EmployeeInformation::find($informationData->input('information_id'));
            $submitInformation->employeeid = $informationData->input('employeeid');
            $submitInformation->fathername = $informationData->input('fathersname');
            $submitInformation->dob = $informationData->input('birthdate');
            $submitInformation->gender = $informationData->input('gender');
            // $submitInformation->cnic = $informationData->input('cnic');
            $submitInformation->dateofexpiry = $informationData->input('cnic_expiry');
            $submitInformation->birthplace = $informationData->input('birth_place');
            $submitInformation->citizenship = $informationData->input('citizenship');
            $submitInformation->passport = $informationData->input('passport');
            $submitInformation->religion = $informationData->input('religion');
            $submitInformation->maritalstatus = $informationData->input('martial_status');
            $submitInformation->permanentaddress = $informationData->input('permanentaddress');
            $submitInformation->currentaddress = $informationData->input('currentaddress');
            $submitInformation->bankname = $informationData->input('bank_names');
            $submitInformation->bankcode = $informationData->input('bankcode');
            $submitInformation->accountno = $informationData->input('accounnumb');
            $submitInformation->accounttitle = $informationData->input('accountitle');
            $submitInformation->ibannumber = $informationData->input('ibannumb');
    
            $employement_history = array();
            for ($i=1; $i <= $informationData->input('employement_history_length') ; $i++) { 
    
                $temp = (object)[
                    'employername' => $informationData['employername'.$i],
                    'designation' => $informationData['designation'.$i],
                    'salaryandbenefits' => $informationData['salaryandbenefits'.$i],
                    'durationstartdate' => $informationData['durationstartdate'.$i],
                    'durationenddate' => $informationData['durationenddate'.$i],
                    'reasonforleaving' => $informationData['reasonforleaving'.$i]
                ];
                array_push($employement_history , $temp);
            }
            $employement_history = json_encode($employement_history);        
            $submitInformation->employementhistory = $employement_history;
    
    
            $academic_credential = array();
            for ($i=1; $i <= $informationData->input('academic_credential_length') ; $i++) { 
    
                $temp = (object)[
                    'degree' => $informationData['degree'.$i],
                    'intitution' => $informationData['intitution'.$i],
                    'majors' => $informationData['majors'.$i],
                    'passingyear' => $informationData['passingyear'.$i],
                    'grade' => $informationData['grade'.$i]
                ];
                array_push($academic_credential , $temp);
            }
            $academic_credential = json_encode($academic_credential);        
            $submitInformation->academiccredential = $academic_credential;
    
    
    
            $professional_qualification = array();
            for ($i=1; $i <= $informationData->input('professional_qualification_length') ; $i++) { 
    
                $temp = (object)[
                    'type' => $informationData['type'.$i],
                    'details' => $informationData['details'.$i],
                    'institute' => $informationData['institute'.$i],
                    'from' => $informationData['from'.$i],
                    'till' => $informationData['till'.$i]
                ];
                array_push($professional_qualification , $temp);
            }
            $professional_qualification = json_encode($professional_qualification);        
            $submitInformation->professionalqualification = $professional_qualification;
    
            $submitInformation->referencename1 = $informationData->input('referencename1');
            $submitInformation->referencedesignation1 = $informationData->input('referencedesignation1');
            $submitInformation->referencecontact1 = $informationData->input('referencecontact1');
            $submitInformation->referencecompany1 = $informationData->input('referencecompany1');
            $submitInformation->referencecompanyaddress1 = $informationData->input('referencecompanyaddress1');
            $submitInformation->referenceofficialemailid1 = $informationData->input('referenceofficialemailid1');
            $submitInformation->referencecapacity1 = $informationData->input('referencecapacity1');
            $submitInformation->referencename2 = $informationData->input('referencename2');
            $submitInformation->referencedesignation2 = $informationData->input('referencedesignation2');
            $submitInformation->referencecontact2 = $informationData->input('referencecontact2');
            $submitInformation->referencecompany2 = $informationData->input('referencecompany2');
            $submitInformation->referencecompanyaddress2 = $informationData->input('referencecompanyaddress2');
            $submitInformation->referenceofficialemailid2 = $informationData->input('referenceofficialemailid2');
            $submitInformation->referencecapacity2 = $informationData->input('referencecapacity2');
            $submitInformation->relativename1 = $informationData->input('relativename1');
            $submitInformation->relativedesignation1 = $informationData->input('relativedesignation1');
            $submitInformation->relativedepartment1 = $informationData->input('relativedepartment1');
            $submitInformation->relativerelationtype1 = $informationData->input('relativerelationtype1');
            $submitInformation->relativename2 = $informationData->input('relativename2');
            $submitInformation->relativedesignation2 = $informationData->input('relativedesignation2');
            $submitInformation->relativedepartment2 = $informationData->input('relativedepartment2');
            $submitInformation->relativerelationtype2 = $informationData->input('relativerelationtype2');
            $submitInformation->appliedbefore = $informationData->input('appliedbefore');
            $submitInformation->whatposition = $informationData->input('whatposition');
            $submitInformation->resultofapplication = $informationData->input('resultofapplication');
            $submitInformation->emergency_name = $informationData->input('emergency_name');
            $submitInformation->emergency_contact_no = $informationData->input('emergency_contact_no');
            $submitInformation->employee_relation = $informationData->input('employee_relation');
            
            $document_name = NULL;
            if($informationData->hasFile('profile_picture')){

                $document = $informationData->file('profile_picture');
                $document_name = $document->getClientOriginalName();
                $document->move(public_path('portal_assets/profile_pictures/'), $document_name);
    
                $submitInformation->profile_avatar = $document_name;
    
            }
            $process = $submitInformation->save();
            if($process == 1){
              
                // Syncing to the external system is best-effort: the information is already saved,
                // so a missing/unreachable APP_SC_URL must not turn the save into an error for the user.
                if(!empty(env('APP_SC_URL'))){
                    try{
                        Http::withHeaders([
                            'access-token' => 'PoRtAlCrMkEy',

                        ])->post(env('APP_SC_URL').'api/add-employee', [
                            'employee_id'           => $employee_record[0]['employee_id'],
                            'employee_code'         =>  $employee_record[0]['employee_code'],
                            'employee_name'         =>  $employee_record[0]['employee_name'],
                            'employee_contactno'    =>  $employee_record[0]['employee_contactno'],
                            'employee_personal_email'        =>  $employee_record[0]['employee_personal_email'],
                            'employee_department'   =>  $employee_record[0]['employee_department'],
                            'employee_designation'  =>  $employee_record[0]['employee_designation'],
                            'profile_avatar'        => $document_name,
                            'employee_department_name' => 'Random Department',
                            'employee_designation_name' => 'Random Designation',
                        ]);
                    }catch(\Exception $sync_exception){
                        $this->create_error_log('Sync Personal Information To External System', $sync_exception);
                    }
                }
               
                return response()->json([
                    'status' => 'TRUE',
                    'msg' => 'Your information has been successfully saved!',
                ]);
            }else{
              

                return response()->json([
                    'status' => 'FALSE',
                    'msg' =>SELF::SERVER_ERROR,
                ]);
            }

        }catch(\Exception $e){
            $this->create_error_log('Save Personal Information Record', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => SELF::SERVER_ERROR,
                
                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => SELF::SERVER_ERROR,
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);
            
            }
           
        }finally{

        	ActivityTracker::track_activity("Save Personal Information Record");
           
        }
	
		
    
    }	
}
