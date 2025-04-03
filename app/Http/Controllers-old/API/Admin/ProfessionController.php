<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class ProfessionController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get all professions
    public function getProfessions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $professions = DB::table('professions as p')
                 ->leftJoin('professions as p2', 'p2.id', '=', 'p.parent_id')
                ->select('p.id', 'p.profession', 'p2.profession as parent_profession','p.status','p.created_at')
                ->where('p.deleted_at', NULL);
            
            /* ->where('p.created_by', $request->user_id) */
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $professions->where('p.profession', 'LIKE', "%{$request->keyword}%")->orWhere('p2.profession', 'LIKE', "%{$request->keyword}%");
            }
            
                $professions = $professions->get()->toArray();
            
            DB::commit();
            $result = array('status' => true, 'message' => (count($professions)) . " Record found", 'data' => $professions);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
   
    ## Function to add/update profession
    public function updateProfessions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'profession' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array(
                'profession' => $request->profession,
                'parent_id' => $request->parent_id,
            );

            if (isset($request->id)) {
                
                $professionExists = DB::table('professions')
                        ->select('id')
                        ->where('profession',  $request->profession)
                        ->where('id', '!=', $request->id)
                        ->where('deleted_at', NULL)
                        ->first();
                if($professionExists)
                {
                    $result = array('status' => false, 'message' => "Profession already exists");
                    return response()->json($result);
                }
                
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('professions')->where('id', $request->id)->update($param);
                $msg = "Profession has been been successfully updated";
            } else {
                
                $professionExists = DB::table('professions')
                        ->select('id')
                        ->where('profession',  $request->profession)
                        ->where('deleted_at', NULL)
                        ->first();
                if($professionExists)
                {
                    $result = array('status' => false, 'message' => "Profession already exists");
                    return response()->json($result);
                }
                
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('professions')->insert($param);
                $msg = "Profession has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete profession
    public function deleteProfession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                $param['deleted_at'] = $this->entryDate;
                DB::table('professions')->where('id', $request->id)->update($param);
                $msg = "Profession has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update profession's status
    public function updateProfessionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('professions')->where('id', $request->id)->update($param);
                $msg = "Profession status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on profession
    public function professionBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            
            if($request->bulk_action == 'delete' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('professions')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Profession(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "1";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('professions')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Profession(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "0";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('professions')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Profession(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all specialties
    public function getSpecialties(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $specialities = DB::table('specialities as s')
            ->leftJoin('professions as p', 'p.id', '=', 's.profession_id')
            ->select('s.id', 's.specialty', 'p.profession','s.created_at','s.status')
            ->where('s.created_by', $request->user_id)
            ->where('s.deleted_at', NULL);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $specialities->where('s.specialty', 'LIKE', "%{$request->keyword}%")->orWhere('p.profession', 'LIKE', "%{$request->keyword}%");
            }
            
            $specialities = $specialities->get()->toArray();
            
            DB::commit();    
            $result = array('status' => true, 'message' => (count($specialities)) . " Record found", 'data' => $specialities);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update specialities
    public function updateSpecialities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'specialty' => 'required',
            'profession_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array(
                'specialty' => $request->specialty,
                'profession_id' => $request->profession_id,
            );

            if (isset($request->id)) {
                
                $specialtyExists = DB::table('specialities')
                        ->select('id')
                        ->where('specialty',  $request->specialty)
                        ->where('profession_id',  $request->profession_id)
                        ->where('id', '!=', $request->id)
                        ->where('deleted_at', NULL)
                        ->first();
                if($specialtyExists)
                {
                    $result = array('status' => false, 'message' => "Speciality already exists");
                    return response()->json($result);
                }
                
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('specialities')->where('id', $request->id)->update($param);
                $msg = "Speciality has been been successfully updated";
            } else {
                
                $specialtyExists = DB::table('specialities')
                        ->select('id')
                        ->where('profession_id',  $request->profession_id)
                        ->where('specialty',  $request->specialty)
                        ->where('deleted_at', NULL)
                        ->first();
                if($specialtyExists)
                {
                    $result = array('status' => false, 'message' => "Speciality already exists");
                    return response()->json($result);
                }
                
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('specialities')->insert($param);
                $msg = "Speciality has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete speciality
    public function deleteSpeciality(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                $param['deleted_at'] = $this->entryDate;
                DB::table('specialities')->where('id', $request->id)->update($param);
                $msg = "Speciality has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update speciality's status
    public function updateSpecialityStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('specialities')->where('id', $request->id)->update($param);
                $msg = "speciality status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on speciality
    public function specialityBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            
            if($request->bulk_action == 'delete' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('specialities')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Speciality(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "1";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('specialities')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Speciality(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "0";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('specialities')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Speciality(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
