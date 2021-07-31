<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Notification;
use App\Notifications\SendSignupLink;
use App\Notifications\SendOtp;
use Validator;
use App\Models\User;
use Auth;

class RegisterController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->username;
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required|max:255',
        ]);
        $response = array('response' => '', 'success'=>false);
        if ($validator->fails()) {
            return $response['response'] = $validator->messages();
        }else{
            if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
                Auth::attempt(['email' => $username, 'password' => $request->password, 'otpverified' => 1]);
            } else {
                Auth::attempt(['user_name' => $username, 'password' => $request->password, 'otpverified' => 1]);
            }
    
            if ( Auth::check() ) {
                $user = Auth::user(); 
                $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
                $success['name'] =  $user->name;
       
                return response()->json(['success' => 'success','user'=>$success], 200);
            } 
            else{ 
                return response()->json(['error' => 'Unauthorised.'], 401);
            } 
        }
        
    }
    public function sendSignupLink(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
        ]);
        $response = array('response' => '', 'success'=>false);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        }else{
            Notification::route('mail', $request->email)
            ->notify(new SendSignupLink($request->email));
            $user=new User();
            $user->email = $request->email;
            $user->save();
            $response['response'] = 'Successfully send link!';
            $response['success'] = true;
        }
        return $response;
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required|max:255',
        ]);
        $user = User::where('email',$request->email)->first();
        $response = array('response' => '', 'success'=>false);
            if ($validator->fails()) {
                $response['response'] = $validator->messages();
            }else{
                if($user) {
                    $otp = random_int(100000, 999999);
                    $user->user_name = $request->username;
                    $user->password=bcrypt(request()->password);
                    $user->user_role= 'user';
                    $user->otp=$otp;
                    $user->save();
                    Notification::send($user, new SendOtp($otp));
                    $response['success'] = true;
                    $response['response'] = 'OTP sent successfully!';
                } else {
                    $response['response'] = 'OTP not send successfully!';
                    $response['success'] = false;
                }
                
        }
        return $response;   
    }

    public function otpverify(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',
        ]);
        $response = array('response' => '', 'success'=>false);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        }else{
            $user = User::where([['email','=', $request->email], ['otp', '=', $request->otp]])->first();
            if($user) {
                $user->otpverified = 1;
                $user->otp = null;
                $user->register_at = date('Y-m-d H:i:s');
                $user->save();
                $response['response'] = 'OTP verify successfully!';
                $response['success'] = true;
            } else {
                $response['response'] = 'OTP not verify successfully!';
                $response['success'] = false;
            }
        }
        return $response;
    }
    public function updateProfile(Request $request) {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'avatar' => ['image','mimes:jpg,png,jpeg,gif','max:200','dimensions:max_width=256,max_height=256'],
        ],
        [
            'avatar.dimensions'    => 'The avatar must be at least 256 x 256 pixels!',
        ]);
        $response = array('response' => '', 'success'=>false);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return $response;
        }
        if($user) {
            
            if($request->avatar) {
                $imageName = time().'.'.$request->avatar->getClientOriginalExtension();
                $request->avatar->move(public_path('/uploadedimages'), $imageName);
                $user->avatar = $imageName;
            }
           
            $user->name = $request->name;
            $user->save();
            $response['response'] = 'Update Profile successfully!';
            $response['success'] = true;
        }
        return $response;
    }
    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $response = array('response' => 'logout successfully!', 'success'=>false);
    }
}
